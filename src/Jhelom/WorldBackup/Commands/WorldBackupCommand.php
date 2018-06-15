<?php
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

namespace Jhelom\WorldBackup\Commands;

use Jhelom\WorldBackup\BackupForms\TopForm;
use Jhelom\WorldBackup\CommandArguments;
use Jhelom\WorldBackup\CommandInvokeException;
use Jhelom\WorldBackup\CommandInvoker;
use Jhelom\WorldBackup\Messages;
use Jhelom\WorldBackup\ServiceException;
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
    /**
     * WorldBackupCommand constructor.
     * @param string $name
     * @param Plugin $owner
     */
    public function __construct(string $name, Plugin $owner)
    {
        parent::__construct($name, $owner);
        $this->setUsage('/wbackup [list|backup|restore|history|set]');
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
            TopForm::send($sender);
            return true;
        } else {
            $operation = strtolower($args->getString(''));

            switch ($operation) {
                case 'backup':
                case 'b':
                    $this->backupOperation($sender, $args);
                    break;

                case 'restore':
                case 'r':
                    $this->restoreOperation($sender, $args);
                    break;

                case 'history':
                case 'h':
                    $this->historyOperation($sender, $args);
                    break;

                case 'set':
                case 'c':
                    $this->setOperation($sender, $args);
                    break;

                case 'list':
                case 'l':
                    $this->listOperation($sender);
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
     * @throws CommandInvokeException
     * @throws ServiceException
     */
    private function restoreOperation(CommandSender $sender, CommandArguments $args): void
    {
        $service = WorldBackupService::getInstance();
        $world = $args->getString();
        $service->notExistsWorldBackupIfThrow($world);
        $historyNumber = $args->getInt();

        if (is_null($historyNumber)) {
            throw new CommandInvokeException(Messages::historyRequired());
        }

        if ($service->existsWorldSource($world)) {
            $service->backup($world, true);
        }

        $service->restore($world, $historyNumber);
        $sender->sendMessage(Messages::restoreCompleted($world, $historyNumber));
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

        $sender->sendMessage('+------+-----------------+');
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
            case 'max':
                $value = $args->getInt();

                if (!is_numeric($value)) {
                    throw new CommandInvokeException(Messages::setMaxInvalid());
                }

                $service->setHistoryMax($value);
                $service->saveSettings();
                $sender->sendMessage(Messages::setMaxCompleted($service->getHistoryMax()));
                break;

            default:
                $sender->sendMessage('=== 現在の設定 === ');
                $sender->sendMessage(Messages::setMax($service->getHistoryMax()));
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
            $revisions = $service->getHistories($world);
            $count = count($revisions);
            $lastBackup = $count === 0 ? '' : $revisions[0];
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
    }
}
