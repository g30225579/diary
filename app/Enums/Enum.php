<?php
namespace App\Enums;

abstract class Enum
{
    static protected $labelMaps = [];

    static public function getLabelMaps()
    {
        return static::$labelMaps;
    }

    static public function keyExists($key)
    {
        return array_key_exists($key, static::$labelMaps);
    }

}
