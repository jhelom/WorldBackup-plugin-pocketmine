<?php /** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

namespace Jhelom\WorldBackup\Services;


use Jhelom\WorldBackup\Main;
use Jhelom\WorldBackup\Messages;
use Jhelom\WorldBackup\ServiceException;
use Jhelom\WorldBackup\Utils\JsonFile;
use Jhelom\WorldBackup\Utils\Log;
use pocketmine\Server;

/**
 * Class WorldBackupService
 * @package Jhelom\WorldBackup\Services
 */
class WorldBackupService
{
    private const HISTORY_MAX = 'history_max';
    private const HISTORY_MAX_MIN = 3;
    private const HISTORY_MAX_MAX = 30;
    private const AUTO_BACKUP = 'auto_backup';
    private const BACKUP_FOLDER = 'backups';
    private const WORLD_FOLDER = 'worlds';
    private const LAST_AUTO_BACKUP = 'last_auto_backup';

    /** @var WorldBackupService|null */
    static private $instance = null;

    private $settings = [];

    private function __construct()
    {
        $this->loadSettings();
    }

    public function loadSettings(): void
    {
        $path = $this->getSettingsPath();

        $this->settings = JsonFile::load($path, [
            self::HISTORY_MAX => self::HISTORY_MAX_MAX,
            self::AUTO_BACKUP => true,
            self::LAST_AUTO_BACKUP => '',
        ]);
    }

    /**
     * @return string
     */
    private function getSettingsPath(): string
    {
        return Main::getInstance()->getDataFolder() . 'worldbackup.json';
    }

    /**
     * @return WorldBackupService
     */
    static public function getInstance(): WorldBackupService
    {
        if (is_null(self::$instance)) {
            self::$instance = new WorldBackupService();
        }

        return self::$instance;
    }

    /**
     * @return bool
     * @throws ServiceException
     */
    public function executeAutoBackup(): bool
    {
        $today = date('Y_m_d');

        if ($this->settings[self::LAST_AUTO_BACKUP] == $today) {
            return false;
        }

        Log::info(Messages::autoBackupStart());

        $this->backupAll(true);
        $this->settings[self::LAST_AUTO_BACKUP] = $today;
        $this->saveSettings();

        Log::info(Messages::autoBackupEnd());
        return true;
    }

    /**
     * @param bool $isAutoBackup
     * @throws ServiceException
     */
    public function backupAll(bool $isAutoBackup = false): void
    {
        foreach ($this->getSourceWorlds() as $world) {
            $this->backup($world, $isAutoBackup);
        }
    }

    /**
     * @return string[]
     * @throws ServiceException
     */
    public function getSourceWorlds(): array
    {
        return $this->enumDirectories($this->getSourceFolder());
    }

    /**
     * @param string $dir
     * @return string[]
     * @throws ServiceException
     */
    private function enumDirectories(string $dir): array
    {
        if (!is_dir($dir)) {
            throw new ServiceException('Directory not found. ' . $dir);
        }

        $items = scandir($dir);

        return array_filter($items, function ($name) {
            if ($name === '.' or $name === '..') {
                return false;
            } else {
                return true;
            }
        });
    }

    /**
     * @return string
     */
    private function getSourceFolder(): string
    {
        return Server::getInstance()->getDataPath() . self::WORLD_FOLDER;
    }

    /**
     * @param string $world
     * @param bool $isAutoBackup
     * @throws ServiceException
     */
    public function backup(string $world, bool $isAutoBackup = false): void
    {
        $this->notExistsWorldSourceIfThrow($world);
        $sourceDir = $this->getSourceFolder() . DIRECTORY_SEPARATOR . $world;

        $date = $isAutoBackup ? date('Y_m_d') : date('Y_m_d_H_i');
        $backupDir = $this->getBackupFolder() . DIRECTORY_SEPARATOR . $world . DIRECTORY_SEPARATOR . $date;

        $this->copyDirectories($sourceDir, $backupDir);
        $this->purgeHistories($world);
    }

    /**
     * @param string $world
     * @throws ServiceException
     */
    public function notExistsWorldSourceIfThrow(?string $world): void
    {
        if ($this->existsWorldSource($world)) {
            return;
        }

        throw new ServiceException(Messages::worldNotFound($world));
    }

    /**
     * @param null|string $world
     * @return bool
     * @throws ServiceException
     */
    public function existsWorldSource(?string $world): bool
    {
        $this->invalidWorldIfThrow($world);
        $dir = $this->getSourceFolder() . DIRECTORY_SEPARATOR . $world;
        return is_dir($dir);
    }

    /**
     * @param null|string $world
     * @throws ServiceException
     */
    public function invalidWorldIfThrow(?string $world): void
    {
        if (is_null($world)) {
            throw new ServiceException(Messages::worldRequired());
        }

        $world = trim($world);

        if ($world === '') {
            throw new ServiceException(Messages::worldRequired());
        }

        if (preg_match('/^[a-zA-Z0-9_\-]+$/', $world)) {
            return;
        }

        throw new ServiceException(Messages::worldInvalid());
    }

    /**
     * @return string
     */
    private function getBackupFolder(): string
    {
        $dir = Main::getInstance()->getDataFolder() . self::BACKUP_FOLDER;

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        return $dir;
    }

    /**
     * @param string $srcDir
     * @param string $dstDir
     * @throws ServiceException
     */
    private function copyDirectories(string $srcDir, string $dstDir): void
    {
        if (!is_dir($srcDir)) {
            throw new ServiceException('Directory not found. ' . $srcDir);
        }

        if (!is_dir($dstDir)) {
            mkdir($dstDir, 0755, true);
        }

        foreach ($this->enumDirectories($srcDir) as $name) {
            $target = $srcDir . DIRECTORY_SEPARATOR . $name;

            if (is_dir($target)) {
                $this->copyDirectories($srcDir . DIRECTORY_SEPARATOR . $name, $dstDir . DIRECTORY_SEPARATOR . $name);
            } else if (is_file($target)) {
                $srcFile = $srcDir . DIRECTORY_SEPARATOR . $name;
                $dstFile = $dstDir . DIRECTORY_SEPARATOR . $name;
                Log::debug('copy: ' . $name);
                copy($srcFile, $dstFile);
            }
        }
    }

    /**
     * @param string $world
     * @throws ServiceException
     */
    public function purgeHistories(string $world): void
    {
        $this->notExistsWorldBackupIfThrow($world);

        $dir = $this->getBackupFolder() . DIRECTORY_SEPARATOR . $world;
        $names = array_slice($this->getHistories($world), $this->getHistoryMax());

        foreach ($names as $name) {
            $this->deleteDirectories($dir . DIRECTORY_SEPARATOR . $name);
        }
    }

    /**
     * @param null|string $world
     * @throws ServiceException
     */
    public function notExistsWorldBackupIfThrow(?string $world): void
    {
        $this->invalidWorldIfThrow($world);
        $dir = $this->getBackupFolder() . DIRECTORY_SEPARATOR . $world;

        if (is_dir($dir)) {
            return;
        }

        throw new ServiceException(Messages::worldNotFound($world));
    }

    /**
     * @param string $world
     * @return string[]
     * @throws ServiceException
     */
    public function getHistories(?string $world): array
    {
        $this->notExistsWorldBackupIfThrow($world);

        $dir = $this->getBackupFolder() . DIRECTORY_SEPARATOR . $world;

        if (!is_dir($dir)) {
            return [];
        }

        $items = $this->enumDirectories($dir);
        return array_reverse($items);
    }

    /**
     * @return int
     */
    public function getHistoryMax(): int
    {
        return $this->settings[self::HISTORY_MAX];
    }

    /**
     * @param string $dir
     * @throws ServiceException
     */
    private function deleteDirectories(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        foreach ($this->enumDirectories($dir) as $name) {
            $target = $dir . DIRECTORY_SEPARATOR . $name;

            if (is_dir($target)) {
                $this->deleteDirectories($target);
            } else if (is_file($target)) {
                unlink($target);
            }
        }

        rmdir($dir);
    }

    public function saveSettings(): void
    {
        $path = $this->getSettingsPath();
        JsonFile::save($path, $this->settings);
    }

    /**
     * @return array
     * @throws ServiceException
     */
    public function getBackupWorlds(): array
    {
        return $this->enumDirectories($this->getBackupFolder());
    }

    /**
     * @param int $max
     */
    public function setHistoryMax(int $max): void
    {
        $this->settings[self::HISTORY_MAX] = max(self::HISTORY_MAX_MIN, min(self::HISTORY_MAX_MAX, $max));
    }

    /**
     * @param string $world
     * @param int $historyNumber
     * @throws ServiceException
     */
    public function restore(?string $world, int $historyNumber): void
    {
        $server = Server::getInstance();

        $this->notExistsWorldBackupIfThrow($world);

        if ($historyNumber < 1) {
            throw new ServiceException(Messages::historyInvalid());
        }

        $date = $this->getHistoryAt($world, $historyNumber);

        if (is_null($date)) {
            throw new ServiceException(Messages::historyNotFound($world, $historyNumber));
        }

        $backupDir = $this->getBackupFolder() . DIRECTORY_SEPARATOR . $world . DIRECTORY_SEPARATOR . $date;

        if (!is_dir($backupDir)) {
            throw new ServiceException(Messages::historyNotFound($world, $historyNumber));
        }

        $sourceDir = $this->getSourceFolder() . DIRECTORY_SEPARATOR . $world;

        if ($server->isLevelLoaded($world)) {
            $level = $server->getLevelByName($world);
            $spawnLocation = $level->getSpawnLocation();

            if ($this->isDefaultWorld($world)) {
                $tempWorld = '_temp' . mt_rand(0, 999);
                $server->generateLevel($tempWorld);
                $server->loadLevel($tempWorld);
                $tempLevel = Server::getInstance()->getLevelByName($tempWorld);
                $server->setDefaultLevel($tempLevel);

                foreach ($level->getPlayers() as $player) {
                    $player->kick(Messages::restoreLogout($world), false);
                }

                $level->unload(true);

                $this->copyDirectories($backupDir, $sourceDir);
                $server->loadLevel($world);
                $level = Server::getInstance()->getLevelByName($world);
                $server->setDefaultLevel($level);

                foreach ($tempLevel->getPlayers() as $player) {
                    $player->teleport($spawnLocation);
                    $player->sendMessage(Messages::restoreTeleport($world));
                }

                $server->unloadLevel($tempLevel, true);
                $this->deleteDirectories($this->getSourceFolder() . $tempWorld);
            } else {

                foreach ($level->getPlayers() as $player) {
                    $player->teleport($spawnLocation);
                    $player->sendMessage(Messages::restoreTeleport($world));
                }

                $level->unload(true);

                try {
                    $this->copyDirectories($backupDir, $sourceDir);
                } finally {
                    Server::getInstance()->loadLevel($world);
                }
            }
        } else {
            $this->copyDirectories($backupDir, $sourceDir);
        }

        $this->purgeHistories($world);
    }

    /**
     * @param string $world
     * @param int $historyNumber
     * @return null|string
     * @throws ServiceException
     */
    public function getHistoryAt(string $world, int $historyNumber): ?string
    {
        $index = $historyNumber - 1;
        $histories = $this->getHistories($world);

        if ($index < 0) {
            return null;
        }

        if (count($histories) <= $index) {
            return null;
        }

        return $histories[$index];
    }

    /**
     * @param string $world
     * @return bool
     */
    public function isDefaultWorld(string $world): bool
    {
        return strtolower(Server::getInstance()->getDefaultLevel()->getName()) === strtolower($world);
    }

    /**
     * @return bool
     */
    public function getAutoBack(): bool
    {
        return $this->settings[self::AUTO_BACKUP];
    }

    /**
     * @param bool $value
     */
    public function setAutoBackup(bool $value): void
    {
        $this->settings[self::AUTO_BACKUP] = $value;
    }
}