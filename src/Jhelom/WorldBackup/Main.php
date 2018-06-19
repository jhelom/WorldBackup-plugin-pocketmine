<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup;


use Jhelom\Core\PluginBaseEx;
use Jhelom\Core\PluginUpdater;
use Jhelom\WorldBackup\Commands\WorldBackupCommand;
use Jhelom\WorldBackup\Services\WorldBackupService;
use pocketmine\event\Listener;
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
        $this->getLogger()->debug('§aonLoad');

        parent::onLoad();
        Main::$instance = $this;

        // config

        $supportedLanguages = ['jpn', 'eng'];

        foreach ($supportedLanguages as $lang) {
            $this->saveResource('messages.' . $lang . '.yml', true);

        }

        $this->saveDefaultConfig();
        $this->reloadConfig();
        $this->config = new Config($this->getDataFolder() . 'config.yml', Config::YAML, []);

        // messages

        $message_file = $this->getDataFolder() . 'messages.' . $this->getServer()->getLanguage()->getLang() . '.yml';

        if (!is_file($message_file)) {
            $message_file = $this->getDataFolder() . 'messages.eng.yml';
        }

        Messages::load($message_file);

        // restore

        WorldBackupService::getInstance()->executeRestorePlan();
    }

    public function onEnable()
    {
        $this->getLogger()->debug('onLoad');
        parent::onEnable();

        $updater = new PluginUpdater($this, self::PLUGIN_DOWNLOAD_URL_DOMAIN, self::PLUGIN_DOWNLOAD_URL_PATH);
        $updater->update();

        // task

        $this->task = new TimerTask();
        $interval = 1200 * 60 * 12; // 1 minutes * 60 * 24 = 12 hour

        // TODO: scheduler
        if (method_exists($this, 'getScheduler')) {
            $this->getScheduler()->scheduleDelayedRepeatingTask($this->task, 1200, $interval);
        } else {
            $this->getLogger()->debug('Scheduler = Server');
            /** @noinspection PhpUndefinedMethodInspection */
            $this->getServer()->getScheduler()->scheduleDelayedRepeatingTask($this->task, 1200, $interval);
        }

        // register

        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        // setup commands

        $this->setupCommands([
            new WorldBackupCommand($this)
        ]);
    }
}

