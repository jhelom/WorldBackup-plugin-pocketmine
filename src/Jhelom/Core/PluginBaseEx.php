<?php
declare(strict_types=1);

namespace Jhelom\Core;

use pocketmine\plugin\PluginBase;

/**
 * Class PluginBaseEx
 * @package Jhelom\Core
 */
abstract class PluginBaseEx extends PluginBase
{
    public function onLoad()
    {
        parent::onLoad();

        Logging::setLogger($this->getLogger());

        $dir = $this->getDataFolder();

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    public function onDisable()
    {
        parent::onDisable();
    }

    /**
     * @param array $commands
     */
    protected function setupCommands(array $commands): void
    {
        foreach ($commands as $command) {
            if ($command instanceof CommandInvoker) {
                $this->getServer()->getCommandMap()->register($command->getName(), $command);
            }
        }
    }
}