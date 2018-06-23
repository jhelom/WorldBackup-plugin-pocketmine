<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup\Libs;

/**
 * Class Value
 */
class Value
{
    /**
     * @param string $key
     * @param array $array
     * @param int|null $default
     * @return int|null
     */
    static public function getInt(string $key, array $array, int $default = null): ?int
    {
        $value = self::get($key, $array, $default);

        if (is_null($value)) {
            return null;
        } else if (is_int($value)) {
            return $value;
        } else {
            return intval($value);
        }
    }

    /**
     * @param string $key
     * @param array $array
     * @param null $default
     * @return mixed|null
     */
    static public function get(string $key, array $array, $default = null)
    {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        } else {
            return $default;
        }
    }

    /**
     * @param string $key
     * @param array $array
     * @param float|null $default
     * @return float|null
     */
    static public function getFloat(string $key, array $array, float $default = null): ?float
    {
        $value = self::get($key, $array, $default);

        if (is_null($value)) {
            return $default;
        } else if (is_float($value)) {
            return $value;
        } else {
            return floatval($value);
        }
    }

    /**
     * @param string $key
     * @param array $array
     * @param string|null $default
     * @return null|string
     */
    static public function getString(string $key, array $array, string $default = null): ?string
    {
        $value = self::get($key, $array, $default);

        if (is_null($value)) {
            return $default;
        } else if (is_string($value)) {
            return $value;
        } else {
            return '' . $value;
        }
    }

    /**
     * @param string $key
     * @param array $array
     * @param bool|null $default
     * @return bool|null
     */
    static public function getBool(string $key, array $array, bool $default = null): ?bool
    {
        $value = self::get($key, $array, $default);

        if (is_null($value)) {
            return $default;
        } else if (is_bool($value)) {
            return $value;
        } else {
            return boolval($value);
        }
    }

    /**
     * @param string $key
     * @param array $array
     * @param array|null $default
     * @return array|null
     */
    static public function getArray(string $key, array $array, array $default = null): ?array
    {
        $value = self::get($key, $array, $default);

        if (is_array($value)) {
            return $value;
        } else {
            return null;
        }
    }
}