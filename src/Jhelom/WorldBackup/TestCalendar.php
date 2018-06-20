<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup;

use DateTimeImmutable;
use pocketmine\plugin\Plugin;

/**
 * Class TestCalendar
 * @package Jhelom\WorldBackup
 */
class TestCalendar implements ICalendar
{
    /** @var Plugin */
    private $plugin;

    /** @var int */
    private $counter = 0;
    private $offsetDays = 0;

    /**
     * TestCalendar constructor.
     * @param Plugin $plugin
     */
    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @return DateTimeImmutable
     * @throws \Exception
     */
    public function getToday(): DateTimeImmutable
    {
        $this->counter++;

        if ($this->counter > 10) {
            $this->offsetDays++;
            $this->counter = 0;
        }

        $now = new DateTimeImmutable();
        $today = $now->modify('+' . $this->offsetDays . ' days');


        $this->plugin->getLogger()->warning('TEST CALENDAR :' . $today->format(ICalendar::DATE_FORMAT));

        return $today;
    }

    /**
     * @return int
     */
    public function getInterval(): int
    {
        return 20 * 10; // 10 seconds
    }
}