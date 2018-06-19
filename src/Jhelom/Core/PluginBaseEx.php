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
        $this->getLogger()->debug('onLoad');
        parent::onLoad();

        $dir = $this->getDataFolder();

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $updater = new PluginUpdater($this, $this->getPluginUpdateUrlDomain(), $this->getPluginUpdateUrlPath());
        $updater->update();

        foreach ($this->getSupportedLanguages() as $lang) {
            $this->saveResource('messages.' . $lang . '.yml', true);
        }
    }

    public function onEnable()
    {
        $this->getLogger()->debug('onEnable');
        parent::onEnable();

        foreach ($this->setupCommands() as $command) {
            if ($command instanceof CommandInvoker) {
                $this->getServer()->getCommandMap()->register($command->getName(), $command);
            }
        }
    }

    /**
     * @return CommandInvoker[]
     */
    abstract protected function setupCommands(): array;

    /**
     * @return string
     */
    abstract protected function getPluginUpdateUrlDomain(): string;

    /**
     * @return string
     */
    abstract protected function getPluginUpdateUrlPath(): string;

    /**
     * @return string[]
     */
    abstract protected function getSupportedLanguages(): array;

    /**
     * @param string $lang
     * @return string
     */
    protected function getMessagesPath(string $lang): string
    {
        return $this->getDataFolder() . 'messages.' . $lang . '.yml';
    }
}