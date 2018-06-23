<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup\Libs;


use pocketmine\plugin\Plugin;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

/**
 * Class PluginMessages
 */
abstract class PluginMessages
{
    private $messages = [];

    /** @var Plugin */
    private $plugin;

    /**
     * PluginMessages constructor.
     * @param Plugin $plugin
     * @param string $path
     */
    public function __construct(Plugin $plugin, string $path)
    {
        $this->plugin = $plugin;

        if (is_file($path)) {
            $this->messages = (new Config($path, Config::YAML, []))->getAll();
        } else {
            $this->plugin->getLogger()->error(StringFormat::format('messages(yml) not found. "{0}"', $path));
        }
    }

    /**
     * @param string $key
     * @param mixed|null ...$args
     * @return string
     */
    final protected function _get(string $key, ... $args): string
    {
        if (!array_key_exists($key, $this->messages)) {
            $this->plugin->getLogger()->warning(StringFormat::format('Message not found. "{0}"', $key));
            return TextFormat::RED . $key . ': ' . join(', ', $args);
        }

        $message = $this->messages[$key];

        return StringFormat::formatEx($message, $args);
    }

}