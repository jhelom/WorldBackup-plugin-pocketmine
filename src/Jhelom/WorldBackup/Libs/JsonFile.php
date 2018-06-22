<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup\Libs;

/**
 * Class JsonFile
 */
class JsonFile
{
    /**
     * @param string $path
     * @param null $default
     * @return mixed|null
     */
    static public function load(string $path, $default = null)
    {
        if (is_file($path)) {
            $json = file_get_contents($path);
            return json_decode($json, true);
        } else {
            self::save($path, $default);
            return $default;
        }
    }

    /**
     * @param string $path
     * @param $data
     */
    static public function save(string $path, $data): void
    {
        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        file_put_contents($path, $json);
    }
}