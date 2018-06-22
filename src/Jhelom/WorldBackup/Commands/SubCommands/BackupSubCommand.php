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
class BackupSubCommand extends SubCommand
{
    private const COMMAND_NAME = 'backup';
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
     * @throws \Jhelom\WorldBackup\Libs\ServiceException
     */
    function onInvoke(CommandSender $sender, CommandArguments $args): void
    {
        if ($sender instanceof Player) {
            $sender->sendMessage($this->main->getMessages()->executeOnConsole());
        } else {
            $world = $args->getString();
            $this->main->getBackupService()->backup($world);
            $sender->sendMessage($this->main->getMessages()->backupCompleted($world));
        }
    }

    /**
     * @return string
     */
    function getName(): string
    {
        return self::COMMAND_NAME;
    }
}