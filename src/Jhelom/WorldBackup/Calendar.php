<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup;

use DateTimeImmutable;


/**
 * Class Calendar
 * @package Jhelom\WorldBackup
 */
class Calendar implements ICalendar
{
    /**
     * @return DateTimeImmutable
     * @throws \Exception
     */
    public function getToday(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }

    /**
     * @return int
     */
    public function getInterval(): int
    {
        return 1200 * 60; // 1 hour
    }
}