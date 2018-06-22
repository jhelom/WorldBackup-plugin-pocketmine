<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup\Libs;

use pocketmine\command\CommandSender;


/**
 * interface ICommandInvoker
 */
interface ICommandInvoker
{
    /**
     * @param CommandSender $sender
     * @param CommandArguments $args
     */
    public function onInvoke(CommandSender $sender, CommandArguments $args): void;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param SubCommand $subCommand
     */
    public function addSubCommand(SubCommand $subCommand): void;

    /**
     * @param CommandSender $sender
     * @param CommandArguments $args
     */
    public function dispatch(CommandSender $sender, CommandArguments $args): void;
}