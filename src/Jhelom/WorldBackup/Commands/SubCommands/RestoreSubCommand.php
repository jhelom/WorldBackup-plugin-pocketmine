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
     * @throws ServiceException
     */
    function onInvoke(CommandSender $sender, CommandArguments $args): void
    {
        if ($sender instanceof Player) {
            $sender->sendMessage($this->main->getMessages()->executeOnConsole());
            return;
        }

        $world = $args->getString();
        $history = $args->getString();

        try {
            $this->main->getBackupService()->notExistsWorldBackupIfThrow($world);
        } catch (ServiceException $e) {
            $sender->sendMessage($e->getMessage());
            (new ListSubCommand($this->main))->onInvoke($sender, $args);
            return;
        }

        try {
            $this->main->getBackupService()->notExistsHistoryIfThrow($world, $history);
        } catch (ServiceException $e) {
            $sender->sendMessage($e->getMessage());
            (new HistorySubCommand($this->main))->onInvoke($sender, new CommandArguments([$world]));
            return;
        }

        $this->main->getBackupService()->restorePlan($world, $history);
        $sender->sendMessage($this->main->getMessages()->restorePlan($world, $history));
    }

    /**
     * @return string
     */
    function getName(): string
    {
        return self::COMMAND_NAME;
    }
}