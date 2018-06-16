<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup;

use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\plugin\Plugin;

/**
 * Class CommandInvoker
 * @package Jhelom\WorldBackup
 */
abstract class CommandInvoker implements CommandExecutor
{
    /** @var Plugin */
    private $plugin;

    /** @var PluginCommand */
    private $command;

    /** @var string */
    private $name;

    /**
     * CommandInvoker constructor.
     * @param string $name
     * @param Plugin $plugin
     */
    public function __construct(string $name, Plugin $plugin)
    {
        $this->plugin = $plugin;
        $this->name = $name;
        $this->command = new class($name, $plugin) extends PluginCommand
        {
        };
        $this->command->setExecutor($this);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return PluginCommand
     */
    public function getCommand(): PluginCommand
    {
        return $this->command;
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