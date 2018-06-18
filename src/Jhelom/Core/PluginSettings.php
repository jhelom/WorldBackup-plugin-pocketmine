<?php
declare(strict_types=1);

namespace Jhelom\Core;

abstract PluginSettings
{
    private $values = [];

    public function save()
    {

    }

    public function load()
    {

    }

    public function getValue(string $key, $default = null)
    {

    }

    public function getInt(string $key): int
    {

    }
}