<?php /** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

namespace Jhelom\WorldBackup\Services;


use Exception;
use Jhelom\Core\JsonFile;
use Jhelom\Core\Logging;
use Jhelom\Core\ServiceException;
use Jhelom\WorldBackup\Main;
use Jhelom\WorldBackup\Messages;
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
    private const BACKUP_FOLDER = 'backups';
    private const WORLD_FOLDER = 'worlds';
    private const LAST_AUTO_BACKUP = 'last_auto_backup';
    private const RESTORE_WORLD = 'restore_world';
    private const RESTORE_HISTORY = 'restore_history';

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
            self::LAST_AUTO_BACKUP => '',
            self::RESTORE_WORLD => '',
            self::RESTORE_HISTORY => ''
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
    public function autoBackup(): bool
    {
        $today = date('Y_m_d');

        if ($this->settings[self::LAST_AUTO_BACKUP] == $today) {
            return false;
        }

        Logging::info(Messages::autoBackupStart());

        $this->backupAll();
        $this->settings[self::LAST_AUTO_BACKUP] = $today;
        $this->saveSettings();

        Logging::info(Messages::autoBackupEnd());
        return true;
    }

    /**
     * @throws ServiceException
     */
    public function backupAll(): void
    {
        foreach ($this->getSourceWorlds() as $world) {
            $this->backup($world);
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

    private function getWorldSourceFolder(string $world): string
    {
        return $this->getSourceFolder() . DIRECTORY_SEPARATOR . $world;
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
     * @param bool $overwrite
     * @throws ServiceException
     */
    public function backup(string $world, bool $overwrite = true): void
    {
        $this->notExistsWorldSourceIfThrow($world);
        $sourceDir = $this->getWorldSourceFolder($world);

        $history = date('Y-m-d');
        $backupDir = $this->getHistoryFolder($world, $history);

        if ($overwrite === false) {
            if (is_dir($backupDir)) {
                return;
            }
        }

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

    public function notExistsHistoryIfThrow(?string $world, ?string $history): void
    {
        $this->notExistsWorldBackupIfThrow($world);
        $this->invalidHistoryIfThrow($history);

        $dir = $this->getHistoryFolder($world, $history);

        if (is_dir($dir)) {
            return;
        }

        throw new ServiceException(Messages::historyNotFound($world, $history));
    }

    /**
     * @param null|string $world
     * @return bool
     * @throws ServiceException
     */
    public function existsWorldSource(?string $world): bool
    {
        $this->invalidWorldIfThrow($world);
        $dir = $this->getWorldSourceFolder($world);
        return is_dir($dir);
    }

    public function invalidHistoryIfThrow(?string $history): void
    {
        if (is_null($history)) {
            throw new ServiceException(Messages::historyRequired());
        }

        if ($history === '') {
            throw new ServiceException(Messages::historyRequired());
        }

        if (preg_match('/^\d{4}\-\d{2}\-\d{2}$/', $history)) {
            return;
        }

        throw new ServiceException(Messages::historyInvalid());
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

    private function getHistoryFolder(string $world, string $history): string
    {
        return $this->getWorldBackupFolder($world) . DIRECTORY_SEPARATOR . $history;
    }

    private function getWorldBackupFolder(string $world): string
    {
        return $this->getBackupFolder() . DIRECTORY_SEPARATOR . $world;
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
                Logging::debug('copy: ' . $name);
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

        $dir = $this->getWorldBackupFolder($world);
        $histories = array_slice($this->getHistories($world), $this->getHistoryMax());

        foreach ($histories as $history) {
            $this->deleteDirectories($dir . DIRECTORY_SEPARATOR . $history);
        }
    }

    /**
     * @param null|string $world
     * @throws ServiceException
     */
    public function notExistsWorldBackupIfThrow(?string $world): void
    {
        $this->invalidWorldIfThrow($world);
        $dir = $this->getWorldBackupFolder($world);

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

        $dir = $this->getWorldBackupFolder($world);

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
     * @param null|string $history
     * @throws ServiceException
     */
    public function restorePlan(?string $world, ?string $history): void
    {
        $this->notExistsHistoryIfThrow($world, $history);

        $this->settings[self::RESTORE_WORLD] = $world;
        $this->settings[self::RESTORE_HISTORY] = $history;
        $this->saveSettings();
    }

    public function executeRestorePlan(): void
    {
        try {
            $world = $this->getRestorePlanWorld();
            $history = $this->getRestorePlanHistory();

            if (is_null($world) || $world === '') {
                return;
            }

            if (is_null($history) || $history === '') {
                return;
            }

            Logging::info(Messages::restoreStart($world, $history));
            $this->backup($world, false);
            $this->restore($world, $history);
            Logging::info(Messages::restoreCompleted($world, $history));
        } catch (Exception $e) {
            Logging::error($e->getMessage());
        } finally {
            $this->settings[self::RESTORE_WORLD] = '';
            $this->settings[self::RESTORE_HISTORY] = '';
            $this->saveSettings();
        }
    }

    public function restore(?string $world, ?string $history): void
    {
        $this->notExistsHistoryIfThrow($world, $history);

        $sourceDir = $this->getSourceFolder() . DIRECTORY_SEPARATOR . $world;
        $backupDir = $this->getBackupFolder() . DIRECTORY_SEPARATOR . $world . DIRECTORY_SEPARATOR . $history;

        $this->deleteDirectories($sourceDir);
        $this->copyDirectories($backupDir, $sourceDir);
        $this->purgeHistories($world);
    }

    public function getRestorePlanWorld(): ?string
    {
        return $this->settings[self::RESTORE_WORLD];
    }

    public function getRestorePlanHistory(): ?string
    {
        return $this->settings[self::RESTORE_HISTORY];
    }

}