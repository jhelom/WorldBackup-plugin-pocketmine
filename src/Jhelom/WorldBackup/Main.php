<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup;

use Jhelom\WorldBackup\Commands\WorldBackupCommand;
use Jhelom\WorldBackup\Forms\Form;
use Jhelom\WorldBackup\Utils\Log;
use Jhelom\WorldBackup\Utils\PluginUpdater;
use pocketmine\event\level\LevelLoadEvent;
use pocketmine\event\level\LevelUnloadEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

/**
 * Class Main
 * @package Jhelom\WorldBackup
 */
class Main extends PluginBase implements Listener
{
    private const PLUGIN_DOWNLOAD_URL_DOMAIN = 'https://github.com';
    private const PLUGIN_DOWNLOAD_URL_PATH = '/jhelom/WorldBackup-plugin-pocketmine/releases';

    /** @var Main */
    static private $instance;
    /** @var Config */
    private $config;
    private $task;

    /**
     * @return Main
     */
    static public function getInstance(): Main
    {
        return Main::$instance;
    }

    public function onLoad()
    {
        $this->getLogger()->debug(TextFormat::GREEN . 'onLoad');
        Main::$instance = $this;
    }

    public function onEnable()
    {
        $updater = new PluginUpdater($this, self::PLUGIN_DOWNLOAD_URL_DOMAIN, self::PLUGIN_DOWNLOAD_URL_PATH);
        $updater->update();

        $this->getLogger()->debug(TextFormat::GREEN . 'onEnable');

        // folder

        $dir = $this->getDataFolder();

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // config

        $this->saveResource('messages.jpn.yml', true);
        $this->saveResource('messages.eng.yml', true);
        $this->saveDefaultConfig();
        $this->reloadConfig();
        $this->config = new Config($this->getDataFolder() . 'config.yml', Config::YAML, []);

        // messages

        $message_file = $this->getDataFolder() . 'messages.' . $this->getServer()->getLanguage()->getLang() . '.yml';

        if (!is_file($message_file)) {
            $message_file = $this->getDataFolder() . 'messages.eng.yml';
        }

        Messages::load($message_file);

        // task

        $this->task = new TimerTask();
        $interval = 1200 * 60; // 1 minutes * 60 = 1 hour

        // TODO: scheduler
        if (method_exists($this, 'getScheduler')) {
            $this->getScheduler()->scheduleDelayedRepeatingTask($this->task, $interval, $interval);
        } else {
            Log::debug('Scheduler = Server');
            /** @noinspection PhpUndefinedMethodInspection */
            $this->getServer()->getScheduler()->scheduleDelayedRepeatingTask($this->task, $interval, $interval);
        }

        // register

        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        // setup commands

        $this->setupCommands();
    }

    private function setupCommands(): void
    {
        /** @var CommandInvoker[] */
        $invokers = [
            new WorldBackupCommand($this)
        ];

        foreach ($invokers as $invoker) {
            if ($invoker instanceof CommandInvoker) {
                $this->getServer()->getCommandMap()->register($invoker->getName(), $invoker->getCommand());
            }
        }
    }

    public function onDisable()
    {
        $this->getLogger()->info(TextFormat::GREEN . 'onDisable');
    }

    /**
     * @param PlayerQuitEvent $event
     */
    public function onPlayerQuit(PlayerQuitEvent $event)
    {
        Form::purge($event->getPlayer()->getLowerCaseName());
    }

    /**
     * @param LevelLoadEvent $event
     */
    public function onLevelLoad(LevelLoadEvent $event)
    {
        Log::debug('LevelLoadEvent:' . $event->getLevel()->getName());
    }


    /**
     * @param LevelUnloadEvent $event
     */
    public function onLevelUnload(LevelUnloadEvent $event)
    {
        Log::debug('LevelUnloadEvent:' . $event->getLevel()->getName());
    }

    /**
     * @param DataPacketReceiveEvent $event
     */
    public function onPacketReceived(DataPacketReceiveEvent $event): void
    {
        Form::process($event);
    }
}

