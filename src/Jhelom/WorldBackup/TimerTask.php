<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup;

use Jhelom\WorldBackup\Libs\ServiceException;
use pocketmine\scheduler\Task;

/**
 * Class TimerTask
 * @package Jhelom\WorldBackup
 */
class TimerTask extends Task
{
    private $main;

    /**
     * TimerTask constructor.
     * @param Main $main
     */
    public function __construct(Main $main)
    {
        $this->main = $main;
    }

    /**
     * Actions to execute when run
     *
     * @param int $currentTick
     *
     * @return void
     * @throws ServiceException
     */
    public function onRun(int $currentTick)
    {
        $this->main->getBackupService()->autoBackup();
    }
}