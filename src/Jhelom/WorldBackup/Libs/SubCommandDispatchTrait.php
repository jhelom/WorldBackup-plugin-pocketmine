<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup\Libs;


use Exception;
use pocketmine\command\CommandSender;

/**
 * Trait SubCommandDispatchTrait
 * @package Jhelom\Core
 */
trait SubCommandDispatchTrait
{
    private $subCommands = [];

    /**
     * @param SubCommand $subCommand
     * @throws Exception
     */
    public function addSubCommand(SubCommand $subCommand): void
    {
        $key = $subCommand->getName();

        if (array_key_exists($key, $this->subCommands)) {
            throw new Exception(StringFormat::format('Already added SubCommand. "{0}" > "{1}"' . $this->getName(), $key));
        }

        $this->subCommands[$key] = $subCommand;
    }

    /**
     * @param CommandSender $sender
     * @param CommandArguments $args
     * @return void
     */
    public function dispatch(CommandSender $sender, CommandArguments $args): void
    {
        $command = $args->peek();

        if (is_null($command)) {
            $this->onInvoke($sender, $args);
            return;
        }

        $command = strtolower($command);

        if (array_key_exists($command, $this->subCommands)) {
            $subCommand = $this->subCommands[$command];
            if ($subCommand instanceof SubCommand) {
                $args->stripFirst();
                $subCommand->dispatch($sender, $args);
            }
        } else {
            $this->onInvoke($sender, $args);
        }
    }
}