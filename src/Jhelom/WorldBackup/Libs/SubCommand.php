<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup\Libs;

use pocketmine\command\CommandSender;


/**
 * Class SubCommand
 */
abstract class SubCommand implements ICommandInvoker
{
    use SubCommandDispatchTrait;

    /**
     * @param CommandSender $sender
     * @param CommandArguments $args
     */
    abstract public function onInvoke(CommandSender $sender, CommandArguments $args): void;

    /**
     * @return string
     */
    abstract public function getName(): string;
}