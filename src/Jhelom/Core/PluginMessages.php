<?php
declare(strict_types=1);

namespace Jhelom\Core;

use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

/**
 * Class PluginMessages
 * @package Jhelom\Core
 */
abstract class PluginMessages
{
    static private $messages = [];

    /**
     * @param string $path
     */
    static public function load(string $path): void
    {
        self::$messages = (new Config($path, Config::YAML, []))->getAll();
    }


    /**
     * @param string $key
     * @param mixed|null ...$args
     * @return string
     */
    static protected function _getMessage(string $key, ... $args): string
    {
        if (!array_key_exists($key, self::$messages)) {
            return TextFormat::RED . $key . ': ' . join(', ', $args);
        }

        $message = self::$messages[$key];

        return StringFormat::formatEx($message, $args);
    }

}