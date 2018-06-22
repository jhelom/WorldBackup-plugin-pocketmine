<?php /** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

namespace Jhelom\WorldBackup\Services;


use DateTimeImmutable;
use Exception;
use Jhelom\WorldBackup\ICalendar;
use Jhelom\WorldBackup\Libs\JsonFile;
use Jhelom\WorldBackup\Libs\ServiceException;
use Jhelom\WorldBackup\Libs\StringFormat;
use Jhelom\WorldBackup\Libs\Value;
use Jhelom\WorldBackup\Main;
use pocketmine\Server;

/**
 * Class WorldBackupService
 * @package Jhelom\WorldBackup\Services
 */
class WorldBackupService
{
    private const HISTORY_LIMIT = 'history_limit';
    private const HISTORY_LIMIT_MIN = 3;
    private const HISTORY_LIMIT_MAX = 30;
    private const HISTORY_LIMIT_DEFAULT = 10;
    private const BACKUP_FOLDER = 'backups';
    private const WORLD_FOLDER = 'worlds';
    private const LAST_BACKUP = 'last_backup';
    private const RESTORE_WORLD = 'restore_world';
    private const RESTORE_HISTORY = 'restore_history';
    private const DAYS = 'days';
    private const DAYS_MIN = 1;
    private const DAYS_MAX = 999;

    private $settings = [];

    /** @var Main */
    private $main;

    /** @var ICalendar */
    private $calendar;

    /**
     * WorldBackupService constructor.
     * @param Main $main
     * @param ICalendar $calendar
     */
    public function __construct(Main $main, ICalendar $calendar)
    {
        $this->main = $main;
        $this->calendar = $calendar;
        $this->loadSettings();
    }

    public function loadSettings(): void
    {
        $path = $this->getSettingsPath();

        $this->settings = JsonFile::load($path, [
            self::HISTORY_LIMIT => self::HISTORY_LIMIT_DEFAULT,
            self::LAST_BACKUP => '',
            self::RESTORE_WORLD => '',
            self::RESTORE_HISTORY => '',
            self::DAYS => 1,
        ]);
    }

    /**
     * @return string
     */
    private function getSettingsPath(): string
    {
        return $this->main->getDataFolder() . 'worldbackup.json';
    }

    /**
     * @return bool
     * @throws ServiceException
     */
    public function autoBackup(): bool
    {
        $today = $this->calendar->getToday();
        $this->main->getLogger()->debug(StringFormat::format('today = {0}', $today->format(ICalendar::DATE_FORMAT)));

        $s = Value::getString(self::LAST_BACKUP, $this->settings, '2000-01-01');
        $this->main->getLogger()->debug(StringFormat::format('last backup string = {0}', $s));

        $last = DateTimeImmutable::createFromFormat(ICalendar::DATE_FORMAT, $s);

        if ($last !== false) {
            $this->main->getLogger()->debug(StringFormat::format('last backup date   = {0}', $last->format(ICalendar::DATE_FORMAT)));
            $diff = $today->diff($last);
            $this->main->getLogger()->debug(StringFormat::format('diff days = {0}', $diff->days));

            if ($diff->days < $this->getDays()) {
                return false;
            }
        }

        $this->main->getLogger()->info($this->main->getMessages()->autoBackupStart());

        $this->backupAll();
        $this->settings[self::LAST_BACKUP] = $today->format(ICalendar::DATE_FORMAT);
        $this->saveSettings();

        $this->main->getLogger()->info($this->main->getMessages()->autoBackupEnd());
        return true;
    }

    /**
     * @return int
     */
    public function getDays(): int
    {
        return Value::getInt(self::DAYS, $this->settings, 1);
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
    public function backup(?string $world, bool $overwrite = true): void
    {
        $this->notExistsWorldSourceIfThrow($world);
        $sourceDir = $this->getWorldSourceFolder($world);

        $history = $this->calendar->getToday()->format(ICalendar::DATE_FORMAT);
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

        throw new ServiceException($this->main->getMessages()->worldNotFound($world));
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

    /**
     * @param null|string $world
     * @throws ServiceException
     */
    public function invalidWorldIfThrow(?string $world): void
    {
        if (is_null($world)) {
            throw new ServiceException($this->main->getMessages()->worldRequired());
        }

        $world = trim($world);

        if ($world === '') {
            throw new ServiceException($this->main->getMessages()->worldRequired());
        }

        if (preg_match('/^[a-zA-Z0-9_\-]+$/', $world)) {
            return;
        }

        throw new ServiceException($this->main->getMessages()->worldInvalid());
    }

    /**
     * @param string $world
     * @return string
     */
    private function getWorldSourceFolder(string $world): string
    {
        return $this->getSourceFolder() . DIRECTORY_SEPARATOR . $world;
    }

    /**
     * @param string $world
     * @param string $history
     * @return string
     */
    private function getHistoryFolder(string $world, string $history): string
    {
        return $this->getWorldBackupFolder($world) . DIRECTORY_SEPARATOR . $history;
    }

    /**
     * @param string $world
     * @return string
     */
    private function getWorldBackupFolder(string $world): string
    {
        return $this->getBackupFolder() . DIRECTORY_SEPARATOR . $world;
    }

    /**
     * @return string
     */
    private function getBackupFolder(): string
    {
        $dir = $this->main->getDataFolder() . self::BACKUP_FOLDER;

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
            $this->main->getLogger()->debug(StringFormat::format('make directory. "{0}"', $dstDir));
            mkdir($dstDir, 0755, true);
        }

        foreach ($this->enumDirectories($srcDir) as $name) {
            $target = $srcDir . DIRECTORY_SEPARATOR . $name;

            if (is_dir($target)) {
                $this->copyDirectories($srcDir . DIRECTORY_SEPARATOR . $name, $dstDir . DIRECTORY_SEPARATOR . $name);
            } else if (is_file($target)) {
                $srcFile = $srcDir . DIRECTORY_SEPARATOR . $name;
                $dstFile = $dstDir . DIRECTORY_SEPARATOR . $name;
                copy($srcFile, $dstFile);
                $this->main->getLogger()->debug(StringFormat::format('copy file. {0} => {1}', $srcFile, $dstFile));
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
        $histories = array_slice($this->getHistories($world), $this->getHistoryLimit());

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

        throw new ServiceException($this->main->getMessages()->worldNotFound($world));
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
    public function getHistoryLimit(): int
    {
        return Value::getInt(self::HISTORY_LIMIT, $this->settings, self::HISTORY_LIMIT_DEFAULT);
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
                $this->main->getLogger()->debug(StringFormat::format('delete file. "{0}"', $target));
                unlink($target);
            }
        }

        $this->main->getLogger()->debug(StringFormat::format('delete directory. "{0}"', $dir));
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
    public function setHistoryLimit(int $max): void
    {
        $this->settings[self::HISTORY_LIMIT] = max(self::HISTORY_LIMIT_MIN, min(self::HISTORY_LIMIT_MAX, $max));
        $this->saveSettings();
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

    /**
     * @param null|string $world
     * @param null|string $history
     * @throws ServiceException
     */
    public function notExistsHistoryIfThrow(?string $world, ?string $history): void
    {
        $this->notExistsWorldBackupIfThrow($world);
        $this->invalidHistoryIfThrow($history);

        $dir = $this->getHistoryFolder($world, $history);

        if (is_dir($dir)) {
            return;
        }

        throw new ServiceException($this->main->getMessages()->historyNotFound($world, $history));
    }

    /**
     * @param null|string $history
     * @throws ServiceException
     */
    public function invalidHistoryIfThrow(?string $history): void
    {
        if (is_null($history)) {
            throw new ServiceException($this->main->getMessages()->historyRequired());
        }

        if ($history === '') {
            throw new ServiceException($this->main->getMessages()->historyRequired());
        }

        if (preg_match('/^\d{4}\-\d{2}\-\d{2}$/', $history)) {
            return;
        }

        throw new ServiceException($this->main->getMessages()->historyInvalid());
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

            $this->main->getLogger()->info($this->main->getMessages()->restoreStart($world, $history));
            $this->backup($world, false);
            $this->restore($world, $history);
            $this->main->getLogger()->info($this->main->getMessages()->restoreCompleted($world, $history));
        } catch (Exception $e) {
            $this->main->getLogger()->logException($e);
        } finally {
            $this->settings[self::RESTORE_WORLD] = '';
            $this->settings[self::RESTORE_HISTORY] = '';
            $this->saveSettings();
        }
    }

    /**
     * @return null|string
     */
    public function getRestorePlanWorld(): ?string
    {
        if (array_key_exists(self::RESTORE_WORLD, $this->settings)) {
            return $this->settings[self::RESTORE_WORLD];
        } else {
            return null;
        }
    }

    /**
     * @return null|string
     */
    public function getRestorePlanHistory(): ?string
    {
        if (array_key_exists(self::RESTORE_HISTORY, $this->settings)) {
            return $this->settings[self::RESTORE_HISTORY];
        } else {
            return null;
        }
    }

    /**
     * @param null|string $world
     * @param null|string $history
     * @throws ServiceException
     */
    public function restore(?string $world, ?string $history): void
    {
        $this->notExistsHistoryIfThrow($world, $history);

        $sourceDir = $this->getWorldSourceFolder($world);
        $backupDir = $this->getHistoryFolder($world, $history);

        $this->deleteDirectories($sourceDir);
        $this->copyDirectories($backupDir, $sourceDir);
        $this->purgeHistories($world);
    }

    public function clearRestore(): void
    {
        $this->settings[self::RESTORE_WORLD] = '';
        $this->settings[self::RESTORE_HISTORY] = '';
        $this->saveSettings();
    }

    /**
     * @param int $days
     */
    public function setDays(int $days): void
    {
        $this->settings[self::DAYS] = max(self::DAYS_MIN, min(self::DAYS_MAX, $days));
        $this->saveSettings();
    }
}