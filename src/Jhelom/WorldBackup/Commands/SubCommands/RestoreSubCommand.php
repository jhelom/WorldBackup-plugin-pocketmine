<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup\Commands\SubCommands;


use Jhelom\WorldBackup\Libs\CommandArguments;
use Jhelom\WorldBackup\Libs\ServiceException;
use Jhelom\WorldBackup\Libs\SubCommand;
use Jhelom\WorldBackup\Main;
use pocketmine\command\CommandSender;
use pocketmine\Player;

/**
 * Class BackupSubCommand
 * @package Jhelom\WorldBackup\Commands\SubCommands
 */
class RestoreSubCommand extends SubCommand
{
    private const COMMAND_NAME = 'restore';

    /**
     * @param CommandSender $sender
     * @param CommandArguments $args
     * @throws ServiceException
     */
    function onInvoke(CommandSender $sender, CommandArguments $args): void
    {
        if ($sender instanceof Player) {
            $sender->sendMessage(Main::getInstance()->getMessages()->executeOnConsole());
            return;
        }

        $world = $args->getString();
        $history = $args->getString();

        try {
            Main::getInstance()->getBackupService()->notExistsWorldBackupIfThrow($world);
        } catch (ServiceException $e) {
            $sender->sendMessage($e->getMessage());
            (new ListSubCommand())->onInvoke($sender, $args);
            return;
        }

        try {
            Main::getInstance()->getBackupService()->notExistsHistoryIfThrow($world, $history);
        } catch (ServiceException $e) {
            $sender->sendMessage($e->getMessage());
            (new HistorySubCommand())->onInvoke($sender, new CommandArguments([$world]));
            return;
        }

        Main::getInstance()->getBackupService()->restorePlan($world, $history);
        $sender->sendMessage(Main::getInstance()->getMessages()->restorePlan($world, $history));
    }

    /**
     * @return string
     */
    function getName(): string
    {
        return self::COMMAND_NAME;
    }
}