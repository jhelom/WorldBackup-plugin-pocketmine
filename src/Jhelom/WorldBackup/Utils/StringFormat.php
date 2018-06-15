<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup\Utils;

/**
 * Class StringFormat
 * @package Jhelom\WorldBackup\utils
 */
class StringFormat
{
    /**
     * @param string $message
     * @param mixed ...$args
     * @return string
     */
    static public function format(string $message, ... $args): string
    {
        foreach ($args as $index => $value) {
            $search = '{' . $index . '}';
            $message = str_replace($search, $value, $message);
        }

        return $message;
    }

    /**
     * @param string $message
     * @param array $args
     * @return string
     */
    static public function formatEx(string $message, array $args = []): string
    {
        foreach ($args as $index => $value) {
            $search = '{' . $index . '}';
            $message = str_replace($search, $value, $message);
        }

        return $message;
    }

}