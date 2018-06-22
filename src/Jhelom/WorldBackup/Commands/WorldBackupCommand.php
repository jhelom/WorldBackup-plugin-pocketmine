<?php
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

namespace Jhelom\WorldBackup\Commands;

use Jhelom\WorldBackup\Commands\SubCommands\BackupSubCommand;
use Jhelom\WorldBackup\Commands\SubCommands\ClearSubCommand;
use Jhelom\WorldBackup\Commands\SubCommands\HistorySubCommand;
use Jhelom\WorldBackup\Commands\SubCommands\ListSubCommand;
use Jhelom\WorldBackup\Commands\SubCommands\RestoreSubCommand;
use Jhelom\WorldBackup\Commands\SubCommands\SetSubCommand;
use Jhelom\WorldBackup\Libs\CommandArguments;
use Jhelom\WorldBackup\Libs\PluginCommandEx;
use Jhelom\WorldBackup\Main;
use pocketmine\command\CommandSender;


/**
 * Class WorldBackupCommand
 * @package Jhelom\WorldBackup\Commands
 */
class WorldBackupCommand extends PluginCommandEx
{
    private const COMMAND_NAME = 'wbackup';
    private $main;

    /**
     * WorldBackupCommand constructor.
     * @param Main $main
     * @throws \Exception
     */
    public function __construct(Main $main)
    {
        parent::__construct(self::COMMAND_NAME, $main);
        $this->main = $main;
        $this->setUsage('/wbackup [list|backup|restore|history|set|clear]');
        $this->setDescription($this->main->getMessages()->commandDescription());
        $this->setPermission('Jhelom.command.wbackup');

        $this->addSubCommand(new BackupSubCommand($main));
        $this->addSubCommand(new ClearSubCommand($main));
        $this->addSubCommand(new HistorySubCommand($main));
        $this->addSubCommand(new RestoreSubCommand($main));
        $this->addSubCommand(new SetSubCommand($main));
        $this->addSubCommand(new ListSubCommand($main));
    }

    /**
     * @param CommandSender $sender
     * @param CommandArguments $args
     */
    function onInvoke(CommandSender $sender, CommandArguments $args): void
    {
        foreach ($this->main->getMessages()->help() as $help) {
            $sender->sendMessage($help);
        }
    }
}
