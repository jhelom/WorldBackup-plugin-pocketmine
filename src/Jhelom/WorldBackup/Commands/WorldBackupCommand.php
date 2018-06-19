<?php
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

namespace Jhelom\WorldBackup\Commands;

use Jhelom\Core\CommandArguments;
use Jhelom\Core\CommandInvokeException;
use Jhelom\Core\CommandInvoker;
use Jhelom\Core\ServiceException;
use Jhelom\WorldBackup\Main;
use pocketmine\command\CommandSender;
use pocketmine\Player;


/**
 * Class WorldBackupCommand
 * @package Jhelom\WorldBackup\Commands
 */
class WorldBackupCommand extends CommandInvoker
{
    private const COMMAND_NAME = 'wbackup';
    private $main;

    /**
     * WorldBackupCommand constructor.
     * @param Main $main
     */
    public function __construct(Main $main)
    {
        parent::__construct(self::COMMAND_NAME, $main);
        $this->main = $main;
        $this->setUsage('/wbackup [list|backup|restore|history|set|clear]');
        $this->setDescription($this->main->getMessages()->commandDescription());
        $this->setPermission('Jhelom.command.wbackup');
    }

    /**
     * @param CommandSender $sender
     * @param CommandArguments $args
     * @return bool
     * @throws CommandInvokeException
     * @throws ServiceException
     */
    protected function onInvoke(CommandSender $sender, CommandArguments $args): bool
    {
        if ($sender instanceof Player) {
            $sender->sendMessage($this->main->getMessages()->executeOnConsole());
            return true;
        } else {
            $operation = strtolower($args->getString(''));

            switch ($operation) {
                case 'backup':
                    $this->backupOperation($sender, $args);
                    break;

                case 'restore':
                    $this->restoreOperation($sender, $args);
                    break;

                case 'history':
                    $this->historyOperation($sender, $args);
                    break;

                case 'set':
                    $this->setOperation($sender, $args);
                    break;

                case 'list':
                    $this->listOperation($sender);
                    break;

                case 'clear':
                    $this->clearOperation($sender);
                    break;

                default:
                    $this->help($sender);
                    break;
            }
        }

        return true;
    }

    /**
     * @param CommandSender $sender
     */
    private function clearOperation(CommandSender $sender): void
    {
        $this->main->getBackupService()->clearRestore();
        $sender->sendMessage($this->main->getMessages()->clearRestore());
    }

    /**
     * @param CommandSender $sender
     * @param CommandArguments $args
     * @throws ServiceException
     */
    private function backupOperation(CommandSender $sender, CommandArguments $args): void
    {
        $world = $args->getString();
        $this->main->getBackupService()->backup($world);
        $sender->sendMessage($this->main->getMessages()->backupCompleted($world));
    }

    /**
     * @param CommandSender $sender
     * @param CommandArguments $args
     * @throws ServiceException
     */
    private function restoreOperation(CommandSender $sender, CommandArguments $args): void
    {
        $world = $args->getString();
        $history = $args->getString();

        try {
            $this->main->getBackupService()->notExistsWorldBackupIfThrow($world);
        } catch (ServiceException $e) {
            $sender->sendMessage($e->getMessage());
            $this->listOperation($sender);
            return;
        }

        try {
            $this->main->getBackupService()->notExistsHistoryIfThrow($world, $history);
        } catch (ServiceException $e) {
            $sender->sendMessage($e->getMessage());
            $this->historyOperation($sender, new CommandArguments([$world]));
            return;
        }

        $this->main->getBackupService()->restorePlan($world, $history);
        $sender->sendMessage($this->main->getMessages()->restorePlan($world, $history));

    }

    /**
     * @param CommandSender $sender
     * @param CommandArguments $args
     * @throws ServiceException
     */
    private function historyOperation(CommandSender $sender, CommandArguments $args): void
    {
        $world = $args->getString('');
        $service = $this->main->getBackupService();
        $histories = $service->getHistories($world);

        $sender->sendMessage($this->main->getMessages()->historyList($world));
        $sender->sendMessage('+-----+------------------+');
        $sender->sendMessage('| No. | BACKUP DATE      |');
        $sender->sendMessage('+-----+------------------+');

        $i = 0;

        foreach ($histories as $history) {
            $i++;
            $s = sprintf('| %-3d | %-16s |', $i, $history);
            $sender->sendMessage($s);
        }

        $sender->sendMessage('+-----+------------------+');
    }

    /**
     * @param CommandSender $sender
     * @param CommandArguments $args
     * @throws CommandInvokeException
     */
    private function setOperation(CommandSender $sender, CommandArguments $args): void
    {
        $action = strtolower($args->getString(''));
        $service = $this->main->getBackupService();

        switch ($action) {
            case 'limit':
                $limit = $args->getInt();

                if (!is_numeric($limit)) {
                    throw new CommandInvokeException($this->main->getMessages()->setLimitInvalid());
                }

                $service->setHistoryLimit($limit);
                $sender->sendMessage($this->main->getMessages()->setLimitCompleted($service->getHistoryLimit()));
                break;

            case 'days':
                $days = $args->getInt();

                if (!is_numeric($days)) {
                    throw new CommandInvokeException($this->main->getMessages()->setDaysInvalid());
                }

                $service->setDays($days);
                $sender->sendMessage($this->main->getMessages()->setDaysCompleted($service->getDays()));
                break;

            default:
                $sender->sendMessage($this->main->getMessages()->showSettings());
                $sender->sendMessage($this->main->getMessages()->setLimit($service->getHistoryLimit()));
                $sender->sendMessage($this->main->getMessages()->setDays($service->getDays()));
                break;
        }
    }

    /**
     * @param CommandSender $sender
     * @throws ServiceException
     */
    private function listOperation(CommandSender $sender): void
    {
        $service = $this->main->getBackupService();
        $worlds = $service->getBackupWorlds();

        $sender->sendMessage('+-----------------+------------------+---------+');
        $sender->sendMessage('| WORLD           | LAST BACKUP      | HISTORY |');
        $sender->sendMessage('+-----------------+------------------+---------+');

        foreach ($worlds as $world) {
            $histories = $service->getHistories($world);
            $count = count($histories);
            $lastBackup = $count === 0 ? '' : $histories[0];
            $sender->sendMessage(sprintf("| %-15s | %-16s | %7d |", $world, $lastBackup, $count));
        }

        $sender->sendMessage('+-----------------+------------------+---------+');
    }

    /**
     * @param CommandSender $sender
     */
    private function help(CommandSender $sender): void
    {
        foreach ($this->main->getMessages()->help() as $help) {
            $sender->sendMessage($help);
        }
    }
}
