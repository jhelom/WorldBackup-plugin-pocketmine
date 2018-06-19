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

    private $plugin;
    private $counter = 0;

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
        $now = new DateTimeImmutable();
        $today = $now->modify('+' . $this->counter . ' days');
        $this->counter++;

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