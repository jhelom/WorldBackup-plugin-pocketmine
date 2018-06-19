<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup;


use Exception;
use Jhelom\Core\CommandInvoker;
use Jhelom\Core\ISupportedLanguage;
use Jhelom\Core\PluginBaseEx;
use Jhelom\WorldBackup\Commands\WorldBackupCommand;
use Jhelom\WorldBackup\Services\WorldBackupService;
use pocketmine\event\level\LevelLoadEvent;
use pocketmine\event\Listener;
use pocketmine\scheduler\Task;

/**
 * Class Main
 * @package Jhelom\WorldBackup
 */
class Main extends PluginBaseEx implements Listener
{
    private const PLUGIN_DOWNLOAD_URL_DOMAIN = 'https://github.com';
    private const PLUGIN_DOWNLOAD_URL_PATH = '/jhelom/WorldBackup-plugin-pocketmine/releases';

    /** @var WorldBackupService */
    private $backupService;

    /** @var Messages */
    private $messages;

    /** @var Task */
    private $task;

    public function onLoad()
    {
        parent::onLoad();

        $this->backupService = new WorldBackupService($this);

        // messages

        $message_file = $this->getMessagesPath($this->getServer()->getLanguage()->getLang());

        if (!is_file($message_file)) {
            $message_file = $this->getMessagesPath(ISupportedLanguage::ENGLISH);
        }

        $this->messages = new Messages($this->getLogger(), $message_file);

        // restore

        try {
            $this->backupService->autoBackup();
            $this->backupService->executeRestorePlan();
        } catch (Exception $e) {
            $this->getLogger()->logException($e);
        }
    }

    public function onEnable()
    {
        parent::onEnable();

        // task

        $this->task = new TimerTask($this);
        $interval = 1200 * 60 * 12; // 1 minutes * 60 * 12 = 12 hour

        // TODO: scheduler
        if (method_exists($this, 'getScheduler')) {
            $this->getScheduler()->scheduleDelayedRepeatingTask($this->task, $interval, $interval);
        } else {
            $this->getLogger()->debug('Scheduler = Server');
            /** @noinspection PhpUndefinedMethodInspection */
            $this->getServer()->getScheduler()->scheduleDelayedRepeatingTask($this->task, $interval, $interval);
        }

        // register

        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    /**
     * @param LevelLoadEvent $event
     */
    public function onLevelLoad(LevelLoadEvent $event)
    {
        $this->getLogger()->debug('LevelLoadEvent: ' . $event->getLevel()->getName());
    }

    /**
     * @return WorldBackupService
     */
    public function getBackupService(): WorldBackupService
    {
        return $this->backupService;
    }

    /**
     * @return Messages
     */
    public function getMessages(): Messages
    {
        return $this->messages;
    }

    /**
     * @return CommandInvoker[]
     */
    protected function setupCommands(): array
    {
        return [
            new WorldBackupCommand($this)
        ];
    }

    /**
     * @return string
     */
    protected function getPluginUpdateUrlDomain(): string
    {
        return self::PLUGIN_DOWNLOAD_URL_DOMAIN;
    }

    /**
     * @return string
     */
    protected function getPluginUpdateUrlPath(): string
    {
        return self::PLUGIN_DOWNLOAD_URL_PATH;
    }

    /**
     * @return string[]
     */
    protected function getSupportedLanguages(): array
    {
        return [
            ISupportedLanguage::ENGLISH,
            ISupportedLanguage::JAPANESE
        ];
    }
}

