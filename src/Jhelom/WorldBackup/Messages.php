<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup;


use Jhelom\Core\PluginMessages;

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
        return $this->_getMessage('description');
    }

    /**
     * @return string
     */
    public function worldRequired(): string
    {
        return $this->_getMessage('world-required');
    }

    /**
     * @return string
     */
    public function worldInvalid(): string
    {
        return $this->_getMessage('world-invalid');
    }

    /**
     * @param string $world
     * @return string
     */
    public function backupCompleted(string $world): string
    {
        return $this->_getMessage('backup-completed', $world);
    }

    /**
     * @return string
     */
    public function clearRestore(): string
    {
        return $this->_getMessage('restore-clear');
    }

    /**
     * @return string
     */
    public function historyRequired(): string
    {
        return $this->_getMessage('history-required');
    }

    /**
     * @param string $world
     * @param string $history
     * @return string
     */
    public function restorePlan(string $world, string $history): string
    {
        return $this->_getMessage('restore-plan', $world, $history);
    }

    /**
     * @param string $world
     * @param string $history
     * @return string
     */
    public function restoreStart(string $world, string $history): string
    {
        return $this->_getMessage('restore-start', $world, $history);
    }

    /**
     * @param string $world
     * @param string $history
     * @return string
     */
    public function restoreCompleted(string $world, string $history): string
    {
        return $this->_getMessage('restore-completed', $world, $history);
    }

    /**
     * @param string $world
     * @return string
     */
    public function historyList(string $world): string
    {
        return $this->_getMessage('history-list', $world);
    }

    /**
     * @return string
     */
    public function setLimitInvalid(): string
    {
        return $this->_getMessage('set-limit-invalid');
    }

    /**
     * @param int $limit
     * @return string
     */
    public function setLimitCompleted(int $limit): string
    {
        return $this->_getMessage('set-limit-completed', $limit);
    }

    /**
     * @param int $days
     * @return string
     */
    public function setDaysCompleted(int $days): string
    {
        return $this->_getMessage('set-days-completed', $days);
    }

    /**
     * @param int $days
     * @return string
     */
    public function setDays(int $days): string
    {
        return $this->_getMessage('set-days', $days);
    }

    /**
     * @return string
     */
    public function setDaysInvalid(): string
    {
        return $this->_getMessage('set-days-invalid');
    }

    /**
     * @param int $limit
     * @return string
     */
    public function setLimit(int $limit): string
    {
        return $this->_getMessage('set-limit', $limit);
    }

    /**
     * @return string
     */
    public function showSettings(): string
    {
        return $this->_getMessage('show-settings');
    }

    /**
     * @return string[]
     */
    public function help(): array
    {
        return [
            $this->_getMessage('help1'),
            $this->_getMessage('help2'),
            $this->_getMessage('help3'),
            $this->_getMessage('help4'),
            $this->_getMessage('help5'),
            $this->_getMessage('help6'),
            $this->_getMessage('help7'),
            $this->_getMessage('help8'),
            $this->_getMessage('help9'),
        ];
    }

    /**
     * @return string
     */
    public function autoBackupStart(): string
    {
        return $this->_getMessage('auto-backup-start');
    }

    /**
     * @return string
     */
    public function autoBackupEnd(): string
    {
        return $this->_getMessage('auto-backup-end');
    }

    /**
     * @param string $world
     * @return string
     */
    public function worldNotFound(string $world): string
    {
        return $this->_getMessage('world-not-found', $world);
    }

    /**
     * @param string $world
     * @param string $history
     * @return string
     */
    public function historyNotFound(string $world, string $history): string
    {
        return $this->_getMessage('history-not-found', $world, $history);
    }

    /**
     * @return string
     */
    public function historyInvalid(): string
    {
        return $this->_getMessage('history-invalid');
    }

    /**
     * @return string
     */
    public function executeOnConsole(): string
    {
        return $this->_getMessage('command-execute-on-console');
    }
}