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
class ClearSubCommand extends SubCommand
{
    private const COMMAND_NAME = 'clear';

    /**
     * @param CommandSender $sender
     * @param CommandArguments $args
     */
    function onInvoke(CommandSender $sender, CommandArguments $args): void
    {
        if ($sender instanceof Player) {
            $sender->sendMessage(Main::getInstance()->getMessages()->executeOnConsole());
            return;
        }

        Main::getInstance()->getBackupService()->clearRestore();
        $sender->sendMessage(Main::getInstance()->getMessages()->clearRestore());
    }

    /**
     * @return string
     */
    function getName(): string
    {
        return self::COMMAND_NAME;
    }
}