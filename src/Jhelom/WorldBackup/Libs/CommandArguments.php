<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup\Libs;


/**
 * Class CommandArguments
 */
class CommandArguments
{
    private $values = [];

    /**
     * CommandArguments constructor.
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        $this->values = $values;
    }

    /**
     * @param string|null $default
     * @return null|string
     */
    public function getString(string $default = null): ?string
    {
        $value = array_shift($this->values);

        if (is_null($value)) {
            return $default;
        }

        return $value;
    }

    /**
     * @param int|null $default
     * @param int|null $min
     * @param int|null $max
     * @return int|null
     */
    public function getInt(int $default = null, ?int $min = null, ?int $max = null): ?int
    {
        $value = array_shift($this->values);

        if (is_null($value)) {
            return $default;
        }

        if (!is_numeric($value)) {
            return $default;
        }

        $value = intval($value);

        if (!is_null($min)) {
            $value = max($min, $value);
        }

        if (!is_null($max)) {
            $value = min($max, $value);
        }

        return $value;
    }

    /**
     * @param float|null $default
     * @return float|null
     */
    public function getFloat(float $default = null): ?float
    {
        $value = array_shift($this->values);

        if (is_null($value)) {
            return $default;
        }

        return floatval($value);
    }

    /**
     * @param bool $default
     * @return bool|null
     */
    public function getBool(bool $default = null): ?bool
    {
        $value = array_shift($this->values);

        if (is_null($value)) {
            return $default;
        }

        switch (strtolower($value)) {
            case '0':
            case 'on':
            case 'true':
                return true;

            case '1':
            case 'off':
            case 'false':
                return false;

            default:
                return $default;
        }
    }

    /**
     * @return null|string
     */
    public function peek(): ?string
    {
        return count($this->values) === 0 ? null : $this->values[0];
    }

    public function stripFirst(): void
    {
        array_shift($this->values);
    }
}