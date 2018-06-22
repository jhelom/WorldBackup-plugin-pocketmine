<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: takashi
 * Date: 2018/06/22
 * Time: 11:00
 */

namespace Jhelom\WorldBackup\Commands\SubCommands;


use Jhelom\WorldBackup\Libs\CommandArguments;
use Jhelom\WorldBackup\Libs\SubCommand;
use Jhelom\WorldBackup\Main;
use pocketmine\command\CommandSender;
use pocketmine\Player;

/**
 * Class BackupSubCommand
 * @package Jhelom\WorldBackup\Commands\SubCommands
 */
class SetSubCommand extends SubCommand
{
    private const COMMAND_NAME = 'set';
    private $main;

    /**
     * BackupSubCommand constructor.
     * @param Main $main
     * @throws \Exception
     */
    public function __construct(Main $main)
    {
        $this->main = $main;
        $this->addSubCommand(new SetLimitSubCommand($main));
        $this->addSubCommand(new SetDaysSubCommand($main));
    }

    /**
     * @param CommandSender $sender
     * @param CommandArguments $args
     */
    function onInvoke(CommandSender $sender, CommandArguments $args): void
    {
        if ($sender instanceof Player) {
            $sender->sendMessage($this->main->getMessages()->executeOnConsole());
            return;
        }

        $service = $this->main->getBackupService();

        $sender->sendMessage($this->main->getMessages()->showSettings());
        $sender->sendMessage($this->main->getMessages()->setLimit($service->getHistoryLimit()));
        $sender->sendMessage($this->main->getMessages()->setDays($service->getDays()));
    }

    /**
     * @return string
     */
    function getName(): string
    {
        return self::COMMAND_NAME;
    }
}