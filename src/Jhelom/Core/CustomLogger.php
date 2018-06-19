<?php
declare(strict_types=1);

namespace Jhelom\Core;


use Logger;
use Throwable;

/**
 * Class CustomLogger
 * @package Jhelom\Core
 */
class CustomLogger
{
    /** @var Logger */
    private $logger;

    /**
     * CustomLogger constructor.
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param string $message
     * @param mixed ...$args
     */
    public function emergency(string $message, ... $args): void
    {
        $this->logger->emergency(StringFormat::formatEx($message, $args));
    }

    /**
     * @param string $message
     * @param mixed ...$args
     */
    public function alert(string $message, ... $args): void
    {
        $this->logger->alert(StringFormat::formatEx($message, $args));
    }

    /**
     * @param string $message
     * @param mixed ...$args
     */
    public function critical(string $message, ... $args): void
    {
        $this->logger->critical(StringFormat::formatEx($message, $args));
    }

    /**
     * @param string $message
     * @param mixed ...$args
     */
    public function error(string $message, ... $args): void
    {
        $this->logger->error(StringFormat::formatEx($message, $args));
    }

    /**
     * @param string $message
     * @param mixed ...$args
     */
    public function warning(string $message, ... $args): void
    {
        $this->logger->warning(StringFormat::formatEx($message, $args));
    }

    /**
     * @param string $message
     * @param mixed ...$args
     */
    public function notice(string $message, ... $args): void
    {
        $this->logger->notice(StringFormat::formatEx($message, $args));
    }

    /**
     * @param string $message
     * @param mixed ...$args
     */
    public function info(string $message, ... $args): void
    {
        $this->logger->info(StringFormat::formatEx($message, $args));
    }

    /**
     * @param string $message
     * @param mixed ...$args
     */
    public function debug(string $message, ... $args): void
    {
        $this->logger->debug(StringFormat::formatEx($message, $args));
    }

    /**
     * @param Throwable $e
     */
    public function logException(Throwable $e): void
    {
        $this->logger->logException($e);
    }
}