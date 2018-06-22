<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup\Libs\Forms;


/**
 * Class CustomFormValues
 */
class CustomFormValues
{
    /** @var array */
    private $values = [];

    /**
     * CustomFormValues constructor.
     * @param array $values
     */
    public function __construct(array $values)
    {
        $this->values = $values;
    }

    /**
     * @param string $tag
     * @return int|null
     */
    public function getInt(string $tag): ?int
    {
        $value = $this->get($tag);

        if (is_null($value)) {
            return null;
        }

        return intval($value);
    }

    /**
     * @param string $tag
     * @return mixed|null
     */
    public function get(string $tag)
    {
        if (array_key_exists($tag, $this->values)) {
            return $this->values[$tag];
        } else {
            return null;
        }
    }

    /**
     * @param string $tag
     * @return bool|null
     */
    public function getBool(string $tag): ?bool
    {
        $value = $this->get($tag);

        if (is_null($value)) {
            return null;
        }

        return boolval($value);
    }

    /**
     * @param string $tag
     * @return null|string
     */
    public function getString(string $tag): ?string
    {
        $value = $this->get($tag);

        if (is_null($value)) {
            return $value;
        }

        return $value;
    }
}
