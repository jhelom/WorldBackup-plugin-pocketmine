<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup\Libs;


use pocketmine\plugin\PluginBase;

/**
 * Class PluginBaseEx
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

        $url = $this->getPluginUpdateUrl();

        if (!is_null($url)) {
            $updater = new PluginUpdater($this, $url);
            $updater->update();
        }

        foreach ($this->getSupportedLanguages() as $lang) {
            $this->saveResource('messages.' . $lang . '.yml', true);
        }
    }

    /**
     * @return string|null
     */
    abstract protected function getPluginUpdateUrl(): ?string;

    /**
     * @return string[]
     */
    abstract protected function getSupportedLanguages(): array;

    public function onEnable()
    {
        $this->getLogger()->debug('onEnable');
        parent::onEnable();

        foreach ($this->setupCommands() as $command) {
            if ($command instanceof PluginCommandEx) {
                $this->getServer()->getCommandMap()->register($command->getName(), $command);
            }
        }
    }

    /**
     * @return PluginCommandEx[]
     */
    abstract protected function setupCommands(): array;

    /**
     * @return string
     */
    protected function getAvailableMessageFilePath(): string
    {
        $languages = [
            $this->getServer()->getLanguage()->getLang(),
            ISupportedLanguage::ENGLISH
        ];

        foreach ($languages as $lang) {
            $path = $this->getMessageFilePath($lang);

            if (is_file($path)) {
                return $path;
            }
        }

        return '';
    }

    /**
     * @param string $lang
     * @return string
     */
    protected function getMessageFilePath(string $lang): string
    {
        return $this->getDataFolder() . 'messages.' . $lang . '.yml';
    }
}