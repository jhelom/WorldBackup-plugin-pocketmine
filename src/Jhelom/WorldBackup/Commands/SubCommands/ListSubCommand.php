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
class ListSubCommand extends SubCommand
{
    private const COMMAND_NAME = 'list';

    /**
     * @param CommandSender $sender
     * @param CommandArguments $args
     * @throws \Jhelom\WorldBackup\Libs\ServiceException
     */
    function onInvoke(CommandSender $sender, CommandArguments $args): void
    {
        if ($sender instanceof Player) {
            $sender->sendMessage(Main::getInstance()->getMessages()->executeOnConsole());
            return;
        }

        $service = Main::getInstance()->getBackupService();
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
     * @return string
     */
    function getName(): string
    {
        return self::COMMAND_NAME;
    }
}