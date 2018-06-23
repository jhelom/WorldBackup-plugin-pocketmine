<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup\Libs;


use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;

/**
 * Class PluginCommandEx
 */
abstract class PluginCommandEx extends PluginCommand implements CommandExecutor, ICommandInvoker
{
    use SubCommandDispatchTrait;

    /**
     * PluginCommandEx constructor.
     * @param string $name
     * @param Plugin $plugin
     */
    public function __construct(string $name, Plugin $plugin)
    {
        parent::__construct($name, $plugin);
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
            $this->dispatch($sender, new CommandArguments($args));
        } /** @noinspection PhpRedundantCatchClauseInspection */ catch (CommandInvokeException | ServiceException $e) {
            $sender->sendMessage(TextFormat::RED . $e->getMessage());
        }

        return true;
    }
}