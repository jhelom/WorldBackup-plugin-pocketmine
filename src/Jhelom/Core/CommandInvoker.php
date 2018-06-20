<?php
declare(strict_types=1);

namespace Jhelom\Core;


use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\plugin\Plugin;

/**
 * Class CommandInvoker
 * @package Jhelom\Core
 */
abstract class CommandInvoker extends PluginCommand implements CommandExecutor
{
    /**
     * CommandInvoker constructor.
     * @param string $commandName
     * @param Plugin $plugin
     */
    public function __construct(string $commandName, Plugin $plugin)
    {
        parent::__construct($commandName, $plugin);
        $this->setExecutor($this);
    }

    /**
     * @param CommandSender $sender
     * @param Command $command
     * @param string $label
     * @param string[] $args
     *
     * @return bool
     */
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        try {
            return $this->onInvoke($sender, new CommandArguments($args));
        } /** @noinspection PhpRedundantCatchClauseInspection */ catch (CommandInvokeException | ServiceException $e) {
            $sender->sendMessage('§c' . $e->getMessage());
        }

        return true;
    }

    /**
     * @param CommandSender $sender
     * @param CommandArguments $args
     * @return bool
     */
    abstract protected function onInvoke(CommandSender $sender, CommandArguments $args): bool;

}