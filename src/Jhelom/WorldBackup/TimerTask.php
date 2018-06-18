<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup;

use Jhelom\Core\ServiceException;
use Jhelom\WorldBackup\Services\WorldBackupService;
use pocketmine\scheduler\Task;

/**
 * Class TimerTask
 * @package Jhelom\WorldBackup
 */
class TimerTask extends Task
{
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
        WorldBackupService::getInstance()->autoBackup();
    }
}