<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup;


use Jhelom\WorldBackup\Utils\StringFormat;
use pocketmine\utils\Config;

/**
 * Class Messages
 * @package Jhelom\WorldBackup
 */
class Messages
{
    static private $messages = [];

    /**
     * @param string $path
     */
    static public function load(string $path): void
    {
        self::$messages = (new Config($path, Config::YAML, []))->getAll();
    }

    /**
     * @return string
     */
    static public function commandProhibited(): string
    {
        return self::_getMessage('command-prohibited');
    }

    /**
     * @param string $key
     * @param mixed|null ...$args
     * @return string
     */
    static private function _getMessage(string $key, ... $args): string
    {
        if (!array_key_exists($key, self::$messages)) {
            return '§c' . $key . ': ' . join(', ', $args);
        }

        $message = self::$messages[$key];

        return StringFormat::formatEx($message, $args);
    }

    /**
     * @return string
     */
    static public function commandInvokeOnPlayer(): string
    {
        return self::_getMessage('command-invoke-on-player');
    }

    /**
     * @return string
     */
    static public function commandInvokeOnConsole(): string
    {
        return self::_getMessage('command-invoke-on-console');
    }

    /**
     * @param string $playerName
     * @param string $serverName
     * @return string
     */
    static public function transfer(string $playerName, string $serverName): string
    {
        return self::_getMessage('command-invoke-on-console', $playerName, $serverName);
    }

    /**
     * @return string
     */
    static public function commandDescription(): string
    {
        return self::_getMessage('description');
    }

    /**
     * @return string
     */
    static public function worldRequired(): string
    {
        return self::_getMessage('world-required');
    }

    /**
     * @return string
     */
    static public function worldInvalid(): string
    {
        return self::_getMessage('world-invalid');
    }

    /**
     * @param string $world
     * @return string
     */
    static public function backupCompleted(string $world): string
    {
        return self::_getMessage('backup-completed', $world);
    }

    /**
     * @return string
     */
    static public function historyRequired(): string
    {
        return self::_getMessage('history-required');
    }

    /**
     * @param string $world
     * @param int $historyNumber
     * @return string
     */
    static public function restoreCompleted(string $world, int $historyNumber): string
    {
        return self::_getMessage('restore-completed', $world, $historyNumber);
    }

    /**
     * @param string $world
     * @return string
     */
    static public function historyList(string $world): string
    {
        return self::_getMessage('history-list', $world);
    }

    /**
     * @return string
     */
    static public function setMaxInvalid(): string
    {
        return self::_getMessage('set-max-invalid');
    }

    /**
     * @param int $max
     * @return string
     */
    static public function setMaxCompleted(int $max): string
    {
        return self::_getMessage('set-max-completed', $max);
    }

    /**
     * @param int $max
     * @return string
     */
    static public function setMax(int $max): string
    {
        return self::_getMessage('set-max', $max);
    }

    /**
     * @return string
     */
    static public function help1(): string
    {
        return self::_getMessage('help1');
    }

    /**
     * @return string
     */
    static public function help2(): string
    {
        return self::_getMessage('help2');
    }

    /**
     * @return string
     */
    static public function help3(): string
    {
        return self::_getMessage('help3');
    }

    /**
     * @return string
     */
    static public function help4(): string
    {
        return self::_getMessage('help4');
    }

    /**
     * @return string
     */
    static public function help5(): string
    {
        return self::_getMessage('help5');
    }

    /**
     * @return string
     */
    static public function help6(): string
    {
        return self::_getMessage('help6');
    }

    /**
     * @return string
     */
    static public function help7(): string
    {
        return self::_getMessage('help7');
    }

    /**
     * @return string
     */
    static public function autoBackupStart(): string
    {
        return self::_getMessage('auto-backup-start');
    }

    /**
     * @return string
     */
    static public function autoBackupEnd(): string
    {
        return self::_getMessage('auto-backup-end');
    }

    /**
     * @param string $world
     * @return string
     */
    static public function worldNotFound(string $world): string
    {
        return self::_getMessage('world-not-found', $world);
    }

    /**
     * @param string $world
     * @param int $historyNumber
     * @return string
     */
    static public function historyNotFound(string $world, int $historyNumber): string
    {
        return self::_getMessage('history-not-found', $world, $historyNumber);
    }

    /**
     * @param string $world
     * @return string
     */
    static public function restoreLogout(string $world): string
    {
        return self::_getMessage('restore-logout', $world);
    }

    /**
     * @param string $world
     * @return string
     */
    static public function restoreTeleport(string $world): string
    {
        return self::_getMessage('restore-teleport', $world);
    }

    /**
     * @return string
     */
    static public function historyInvalid(): string
    {
        return self::_getMessage('history-invalid');
    }

    /**
     * @return string
     */
    static public function backupConfirmFormTitle(): string
    {
        return self::_getMessage('form-backup-confirm-title');
    }

    /**
     * @param string $world
     * @return string
     */
    static public function backupConfirmFormContent(string $world): string
    {
        return self::_getMessage('form-backup-confirm-content', $world);
    }

    /**
     * @return string
     */
    static public function backupFormTitle(): string
    {
        return self::_getMessage('form-backup-title');
    }

    /**
     * @return string
     */
    static public function backupFormContent(): string
    {
        return self::_getMessage('form-backup-content');
    }

    /**
     * @return string
     */
    static public function restoreConfirmFormTitle(): string
    {
        return self::_getMessage('form-restore-confirm-title');
    }

    /**
     * @param string $world
     * @param int $historyNumber
     * @param string $historyDate
     * @return string
     */
    static public function restoreConfirmFormContent(string $world, int $historyNumber, string $historyDate): string
    {
        return self::_getMessage('form-restore-confirm-content', $world, $historyNumber, $historyDate);
    }

    /**
     * @return string
     */
    static public function restoreFormTitle(): string
    {
        return self::_getMessage('form-restore-title');
    }

    /**
     * @return string
     */
    static public function restoreFormContent(): string
    {
        return self::_getMessage('form-restore-content');
    }

    /**
     * @return string
     */
    static public function restoreHistoryFormTitle(): string
    {
        return self::_getMessage('form-restore-history-title');
    }

    /**
     * @param string $world
     * @return string
     */
    static public function restoreHistoryFormContent(string $world): string
    {
        return self::_getMessage('form-restore-history-content', $world);
    }

    /**
     * @return string
     */
    static public function settingsFormTitle(): string
    {
        return self::_getMessage('form-settings-title');
    }

    /**
     * @return string
     */
    static public function topFormTitle(): string
    {
        return self::_getMessage('form-top-title');
    }

    /**
     * @return string
     */
    static public function topFormBackupButton(): string
    {
        return self::_getMessage('form-top-backup-button');
    }

    /**
     * @return string
     */
    static public function topFormRestoreButton(): string
    {
        return self::_getMessage('form-top-restore-button');
    }

    /**
     * @return string
     */
    static public function topFormSettingsButton(): string
    {
        return self::_getMessage('form-top-settings-button');
    }

    /**
     * @return string
     */
    static public function topFormQuitButton(): string
    {
        return self::_getMessage('form-top-quit-button');
    }
}