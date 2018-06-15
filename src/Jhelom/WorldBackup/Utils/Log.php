<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup\Utils;

use Exception;
use Jhelom\WorldBackup\Main;
use pocketmine\plugin\PluginLogger;

/**
 * Class Log
 * @package Jhelom\WorldBackup\Utils
 */
class Log
{
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
        return Main::getInstance()->getLogger();
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