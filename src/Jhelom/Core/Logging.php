<?php
declare(strict_types=1);

namespace Jhelom\Core;

use Exception;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginLogger;

/**
 * Class Logging
 * @package Jhelom\Core
 */
class Logging
{
    /** @var Plugin */
    static private $plugin;

    /**
     * @param Plugin $plugin
     */
    static public function init(Plugin $plugin): void
    {
        self::$plugin = $plugin;
    }

    /**
     * @param string $message
     * @param mixed ...$args
     */
    static public function debug(string $message, ... $args): void
    {
        self::getLogger()->debug(StringFormat::formatEx($message, $args));
    }

    /**
     * @return PluginLogger
     */
    static public function getLogger(): PluginLogger
    {
        return self::$plugin->getLogger();
    }

    /**
     * @param string $message
     * @param mixed ...$args
     */
    static public function info(string $message, ... $args): void
    {
        self::getLogger()->info(StringFormat::formatEx($message, $args));
    }

    /**
     * @param string $message
     * @param mixed ...$args
     */
    static public function warning(string $message, ... $args): void
    {
        self::getLogger()->warning(StringFormat::formatEx($message, $args));
    }

    /**
     * @param string $message
     * @param mixed ...$args
     */
    static public function error(string $message, ... $args): void
    {
        self::getLogger()->error(StringFormat::formatEx($message, $args));
    }

    /**
     * @param Exception $exception
     */
    static public function logException(Exception $exception): void
    {
        self::getLogger()->logException($exception);
    }
}