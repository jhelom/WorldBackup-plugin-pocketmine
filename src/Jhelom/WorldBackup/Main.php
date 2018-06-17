<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup;

use Jhelom\Core\Forms\Form;
use Jhelom\Core\PluginBaseEx;
use Jhelom\Core\PluginUpdater;
use Jhelom\WorldBackup\Commands\WorldBackupCommand;
use pocketmine\event\level\LevelLoadEvent;
use pocketmine\event\level\LevelUnloadEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\utils\Config;

/**
 * Class Main
 * @package Jhelom\WorldBackup
 */
class Main extends PluginBaseEx implements Listener
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
        Main::$instance = $this;
    }

    public function onEnable()
    {
        parent::onEnable();

        $updater = new PluginUpdater($this, self::PLUGIN_DOWNLOAD_URL_DOMAIN, self::PLUGIN_DOWNLOAD_URL_PATH);
        $updater->update();

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
        $interval = 1200 * 60 * 12; // 1 minutes * 60 * 24 = 12 hour

        // TODO: scheduler
        if (method_exists($this, 'getScheduler')) {
            $this->getScheduler()->scheduleDelayedRepeatingTask($this->task, 1000, $interval);
        } else {
            $this->getLogger()->debug('Scheduler = Server');
            /** @noinspection PhpUndefinedMethodInspection */
            $this->getServer()->getScheduler()->scheduleDelayedRepeatingTask($this->task, 1000, $interval);
        }

        // register

        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        // setup commands

        $this->setupCommands([
            new WorldBackupCommand($this)
        ]);
    }

    /**
     * @param PlayerQuitEvent $event
     */
    public function onPlayerQuit(PlayerQuitEvent $event)
    {
        Form::purge($event->getPlayer()->getLowerCaseName());
    }

    public function onDisable()
    {
        parent::onDisable();
        $this->getLogger()->info('§aonDisable');
    }

    /**
     * @param LevelLoadEvent $event
     */
    public function onLevelLoad(LevelLoadEvent $event)
    {
        $this->getLogger()->info('§aLevelLoadEvent:' . $event->getLevel()->getName());
    }

    /**
     * @param LevelUnloadEvent $event
     */
    public function onLevelUnload(LevelUnloadEvent $event)
    {
        $this->getLogger()->info('§aLevelUnloadEvent:' . $event->getLevel()->getName());
    }

    /**
     * @param DataPacketReceiveEvent $event
     */
    public function onPacketReceived(DataPacketReceiveEvent $event): void
    {
        Form::process($event);
    }
}

