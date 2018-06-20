<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup;


use DateTimeImmutable;

/**
 * Interface ICalendar
 * @package Jhelom\WorldBackup
 */
interface ICalendar
{
    public const DATE_FORMAT = 'Y-m-d';

    /**
     * @return DateTimeImmutable
     */
    public function getToday(): DateTimeImmutable;

    /**
     * @return int
     */
    public function getInterval(): int;
}