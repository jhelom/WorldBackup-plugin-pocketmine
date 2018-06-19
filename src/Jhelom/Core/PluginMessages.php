<?php
declare(strict_types=1);

namespace Jhelom\Core;

use Logger;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

/**
 * Class PluginMessages
 * @package Jhelom\Core
 */
abstract class PluginMessages
{
    /** @var CustomLogger */
    protected $logger;
    private $messages = [];

    /**
     * PluginMessages constructor.
     * @param string $path
     * @param Logger $logger
     */
    public function __construct(Logger $logger, string $path)
    {
        $this->logger = new CustomLogger($logger);

        if (is_file($path)) {
            $this->messages = (new Config($path, Config::YAML, []))->getAll();
        } else {
            $this->logger->warning('File not found. "{0}"', $path);
        }
    }

    /**
     * @param string $key
     * @param mixed|null ...$args
     * @return string
     */
    final protected function _getMessage(string $key, ... $args): string
    {
        if (!array_key_exists($key, $this->messages)) {
            $this->logger->warning('Message not found. "{0}"', $key);
            return TextFormat::RED . $key . ': ' . join(', ', $args);
        }

        $message = $this->messages[$key];

        return StringFormat::formatEx($message, $args);
    }

}