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
class SetDaysSubCommand extends SubCommand
{
    private const COMMAND_NAME = 'days';

    /**
     * @param CommandSender $sender
     * @param CommandArguments $args
     * @throws CommandInvokeException
     */
    function onInvoke(CommandSender $sender, CommandArguments $args): void
    {
        $days = $args->getInt();

        if (!is_numeric($days)) {
            throw new CommandInvokeException(Main::getInstance()->getMessages()->setDaysInvalid());
        }

        Main::getInstance()->getBackupService()->setDays($days);
        $sender->sendMessage(Main::getInstance()->getMessages()->setDaysCompleted(Main::getInstance()->getBackupService()->getDays()));
    }

    /**
     * @return string
     */
    function getName(): string
    {
        return self::COMMAND_NAME;
    }
}