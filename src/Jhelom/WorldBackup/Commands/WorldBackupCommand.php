<?php
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

namespace Jhelom\WorldBackup\Commands;

use Jhelom\Core\CommandArguments;
use Jhelom\Core\CommandInvokeException;
use Jhelom\Core\CommandInvoker;
use Jhelom\Core\ServiceException;
use Jhelom\WorldBackup\Messages;
use Jhelom\WorldBackup\Services\WorldBackupService;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\plugin\Plugin;


/**
 * Class WorldBackupCommand
 * @package Jhelom\WorldBackup\Commands
 */
class WorldBackupCommand extends CommandInvoker
{
    private const COMMAND_NAME = 'wbackup';

    /**
     * WorldBackupCommand constructor.
     * @param Plugin $plugin
     */
    public function __construct(Plugin $plugin)
    {
        parent::__construct(self::COMMAND_NAME, $plugin);
        $this->setUsage('/wbackup [list|backup|restore|history|set|clear]');
        $this->setDescription(Messages::commandDescription());
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
            $sender->sendMessage(Messages::executeOnConsole());
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
        $service = WorldBackupService::getInstance();
        $service->clearRestore();
        $sender->sendMessage(Messages::clearRestore());
    }

    /**
     * @param CommandSender $sender
     * @param CommandArguments $args
     * @throws ServiceException
     */
    private function backupOperation(CommandSender $sender, CommandArguments $args): void
    {
        $world = $args->getString();
        WorldBackupService::getInstance()->backup($world);
        $sender->sendMessage(Messages::backupCompleted($world));
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

        $service = WorldBackupService::getInstance();

        try {
            $service->notExistsWorldBackupIfThrow($world);
        } catch (ServiceException $e) {
            $sender->sendMessage($e->getMessage());
            $this->listOperation($sender);
            return;
        }

        try {
            $service->notExistsHistoryIfThrow($world, $history);
        } catch (ServiceException $e) {
            $sender->sendMessage($e->getMessage());
            $this->historyOperation($sender, new CommandArguments([$world]));
            return;
        }

        $service->restorePlan($world, $history);
        $sender->sendMessage(Messages::restorePlan($world, $history));

    }

    /**
     * @param CommandSender $sender
     * @param CommandArguments $args
     * @throws ServiceException
     */
    private function historyOperation(CommandSender $sender, CommandArguments $args): void
    {
        $world = $args->getString('');
        $service = WorldBackupService::getInstance();
        $histories = $service->getHistories($world);

        $sender->sendMessage(Messages::historyList($world));
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
        $service = WorldBackupService::getInstance();

        switch ($action) {
            case 'limit':
                $limit = $args->getInt();

                if (!is_numeric($limit)) {
                    throw new CommandInvokeException(Messages::setLimitInvalid());
                }

                $service->setHistoryLimit($limit);
                $sender->sendMessage(Messages::setLimitCompleted($service->getHistoryLimit()));
                break;

            case 'days':
                $days = $args->getInt();

                if (!is_numeric($days)) {
                    throw new CommandInvokeException(Messages::setDaysInvalid());
                }

                $service->setDays($days);
                $sender->sendMessage(Messages::setCycleCompleted($service->getDays()));
                break;

            default:
                $sender->sendMessage(Messages::showSettings());
                $sender->sendMessage(Messages::setLimit($service->getHistoryLimit()));
                $sender->sendMessage(Messages::setDays($service->getDays()));
                break;
        }
    }

    /**
     * @param CommandSender $sender
     * @throws ServiceException
     */
    private function listOperation(CommandSender $sender): void
    {
        $service = WorldBackupService::getInstance();
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
        $sender->sendMessage(Messages::help1());
        $sender->sendMessage(Messages::help2());
        $sender->sendMessage(Messages::help3());
        $sender->sendMessage(Messages::help4());
        $sender->sendMessage(Messages::help5());
        $sender->sendMessage(Messages::help6());
        $sender->sendMessage(Messages::help7());
        $sender->sendMessage(Messages::help8());
        $sender->sendMessage(Messages::help9());
    }
}
