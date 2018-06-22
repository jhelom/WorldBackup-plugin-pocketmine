<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup\Commands\SubCommands;


use Jhelom\WorldBackup\Libs\CommandArguments;
use Jhelom\WorldBackup\Libs\CommandInvokeException;
use Jhelom\WorldBackup\Libs\SubCommand;
use Jhelom\WorldBackup\Main;
use pocketmine\command\CommandSender;


/**
 * Class BackupSubCommand
 * @package Jhelom\WorldBackup\Commands\SubCommands
 */
class SetLimitSubCommand extends SubCommand
{
    private const COMMAND_NAME = 'limit';
    private $main;

    /**
     * BackupSubCommand constructor.
     * @param Main $main
     */
    public function __construct(Main $main)
    {
        $this->main = $main;
    }

    /**
     * @param CommandSender $sender
     * @param CommandArguments $args
     * @throws CommandInvokeException
     */
    function onInvoke(CommandSender $sender, CommandArguments $args): void
    {
        $limit = $args->getInt();

        if (!is_numeric($limit)) {
            throw new CommandInvokeException($this->main->getMessages()->setLimitInvalid());
        }

        $this->main->getBackupService()->setHistoryLimit($limit);
        $sender->sendMessage($this->main->getMessages()->setLimitCompleted($this->main->getBackupService()->getHistoryLimit()));
    }

    /**
     * @return string
     */
    function getName(): string
    {
        return self::COMMAND_NAME;
    }
}