<?php
declare(strict_types=1);

namespace Jhelom\Core;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

/**
 * Class PluginBaseEx
 * @package Jhelom\Core
 */
abstract class PluginBaseEx extends PluginBase
{
    public function onEnable()
    {
        $this->getLogger()->debug(TextFormat::GREEN . 'onEnable');

        parent::onEnable();

        Logging::init($this);

        $dir = $this->getDataFolder();

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    public function onDisable()
    {
        parent::onDisable();
        $this->getLogger()->debug(TextFormat::GREEN . 'onDisable');
    }

    /**
     * @param CommandInvoker[] $invokers
     */
    protected function setupCommands(array $invokers): void
    {
        foreach ($invokers as $invoker) {
            if ($invoker instanceof CommandInvoker) {
                $this->getServer()->getCommandMap()->register($invoker->getName(), $invoker->getCommand());
            }
        }
    }
}