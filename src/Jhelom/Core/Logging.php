<?php
declare(strict_types=1);

namespace Jhelom\Core;

use Exception;
use Logger;
use pocketmine\Server;
use pocketmine\utils\TextFormat;


/**
 * Class Logging
 * @package Jhelom\Core
 */
class Logging
{
    /** @var Logger */
    static private $logger = null;

    /**
     * @param Logger $logger
     */
    static public function setLogger(Logger $logger): void
    {
        self::$logger = $logger;
    }

    /**
     * @param string $message
     * @param mixed ...$args
     */
    static public function debug(string $message, ... $args): void
    {
        self::getLogger()->debug(TextFormat::GRAY . StringFormat::formatEx($message, $args));
    }

    /**
     * @return Logger
     */
    static public function getLogger(): Logger
    {
        if (is_null(self::$logger)) {
            return Server::getInstance()->getLogger();
        }

        return self::$logger;
    }

    /**
     * @param string $message
     * @param mixed ...$args
     */
    static public function notice(string $message, ... $args): void
    {
        self::getLogger()->notice(StringFormat::formatEx($message, $args));
    }

    /**
     * @param string $message
     * @param mixed ...$args
     */
    static public function info(string $message, ... $args): void
    {
        self::getLogger()->info(TextFormat::GREEN . StringFormat::formatEx($message, $args));
    }

    /**
     * @param string $message
     * @param mixed ...$args
     */
    static public function warning(string $message, ... $args): void
    {
        self::getLogger()->warning(TextFormat::YELLOW . StringFormat::formatEx($message, $args));
    }

    /**
     * @param string $message
     * @param mixed ...$args
     */
    static public function error(string $message, ... $args): void
    {
        self::getLogger()->error(TextFormat::RED . StringFormat::formatEx($message, $args));
    }

    /**
     * @param Exception $exception
     */
    static public function logException(Exception $exception): void
    {
        self::getLogger()->logException($exception);
    }
}