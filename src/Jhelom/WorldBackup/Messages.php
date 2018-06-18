<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup;


use Jhelom\Core\StringFormat;
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
    static public function commandDescription(): string
    {
        return self::_getMessage('description');
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
    static public function clearRestore(): string
    {
        return self::_getMessage('restore-clear');
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
     * @param string $history
     * @return string
     */
    static public function restorePlan(string $world, string $history): string
    {
        return self::_getMessage('restore-plan', $world, $history);
    }

    /**
     * @param string $world
     * @param string $history
     * @return string
     */
    static public function restoreStart(string $world, string $history): string
    {
        return self::_getMessage('restore-start', $world, $history);
    }

    /**
     * @param string $world
     * @param string $history
     * @return string
     */
    static public function restoreCompleted(string $world, string $history): string
    {
        return self::_getMessage('restore-completed', $world, $history);
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
    static public function showSettings(): string
    {
        return self::_getMessage('show-settings');
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
    static public function help8(): string
    {
        return self::_getMessage('help8');
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
     * @param string $history
     * @return string
     */
    static public function historyNotFound(string $world, string $history): string
    {
        return self::_getMessage('history-not-found', $world, $history);
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
    static public function executeOnConsole(): string
    {
        return self::_getMessage('command-execute-on-console');
    }
}