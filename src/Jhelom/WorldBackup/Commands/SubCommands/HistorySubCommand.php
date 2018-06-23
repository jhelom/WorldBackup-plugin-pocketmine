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
class HistorySubCommand extends SubCommand
{
    private const COMMAND_NAME = 'history';

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

        $world = $args->getString('');
        $service = Main::getInstance()->getBackupService();
        $histories = $service->getHistories($world);

        $sender->sendMessage(Main::getInstance()->getMessages()->historyList($world));
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
     * @return string
     */
    function getName(): string
    {
        return self::COMMAND_NAME;
    }
}