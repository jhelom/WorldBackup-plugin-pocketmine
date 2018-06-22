<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup;


use Jhelom\WorldBackup\Libs\PluginMessages;

/**
 * Class Messages
 * @package Jhelom\WorldBackup
 */
class Messages extends PluginMessages
{
    /**
     * @return string
     */
    public function commandDescription(): string
    {
        return $this->_get('description');
    }

    /**
     * @return string
     */
    public function worldRequired(): string
    {
        return $this->_get('world-required');
    }

    /**
     * @return string
     */
    public function worldInvalid(): string
    {
        return $this->_get('world-invalid');
    }

    /**
     * @param string $world
     * @return string
     */
    public function backupCompleted(string $world): string
    {
        return $this->_get('backup-completed', $world);
    }

    /**
     * @return string
     */
    public function clearRestore(): string
    {
        return $this->_get('restore-clear');
    }

    /**
     * @return string
     */
    public function historyRequired(): string
    {
        return $this->_get('history-required');
    }

    /**
     * @param string $world
     * @param string $history
     * @return string
     */
    public function restorePlan(string $world, string $history): string
    {
        return $this->_get('restore-plan', $world, $history);
    }

    /**
     * @param string $world
     * @param string $history
     * @return string
     */
    public function restoreStart(string $world, string $history): string
    {
        return $this->_get('restore-start', $world, $history);
    }

    /**
     * @param string $world
     * @param string $history
     * @return string
     */
    public function restoreCompleted(string $world, string $history): string
    {
        return $this->_get('restore-completed', $world, $history);
    }

    /**
     * @param string $world
     * @return string
     */
    public function historyList(string $world): string
    {
        return $this->_get('history-list', $world);
    }

    /**
     * @return string
     */
    public function setLimitInvalid(): string
    {
        return $this->_get('set-limit-invalid');
    }

    /**
     * @param int $limit
     * @return string
     */
    public function setLimitCompleted(int $limit): string
    {
        return $this->_get('set-limit-completed', $limit);
    }

    /**
     * @param int $days
     * @return string
     */
    public function setDaysCompleted(int $days): string
    {
        return $this->_get('set-days-completed', $days);
    }

    /**
     * @param int $days
     * @return string
     */
    public function setDays(int $days): string
    {
        return $this->_get('set-days', $days);
    }

    /**
     * @return string
     */
    public function setDaysInvalid(): string
    {
        return $this->_get('set-days-invalid');
    }

    /**
     * @param int $limit
     * @return string
     */
    public function setLimit(int $limit): string
    {
        return $this->_get('set-limit', $limit);
    }

    /**
     * @return string
     */
    public function showSettings(): string
    {
        return $this->_get('show-settings');
    }

    /**
     * @return string[]
     */
    public function help(): array
    {
        return [
            $this->_get('help1'),
            $this->_get('help2'),
            $this->_get('help3'),
            $this->_get('help4'),
            $this->_get('help5'),
            $this->_get('help6'),
            $this->_get('help7'),
            $this->_get('help8'),
            $this->_get('help9'),
        ];
    }

    /**
     * @return string
     */
    public function autoBackupStart(): string
    {
        return $this->_get('auto-backup-start');
    }

    /**
     * @return string
     */
    public function autoBackupEnd(): string
    {
        return $this->_get('auto-backup-end');
    }

    /**
     * @param string $world
     * @return string
     */
    public function worldNotFound(string $world): string
    {
        return $this->_get('world-not-found', $world);
    }

    /**
     * @param string $world
     * @param string $history
     * @return string
     */
    public function historyNotFound(string $world, string $history): string
    {
        return $this->_get('history-not-found', $world, $history);
    }

    /**
     * @return string
     */
    public function historyInvalid(): string
    {
        return $this->_get('history-invalid');
    }

    /**
     * @return string
     */
    public function executeOnConsole(): string
    {
        return $this->_get('command-execute-on-console');
    }
}