<?php

namespace App;

class StrategyFinder
{
    public static function get($type)
    {
        return array_get(
            static::AVAILABLE_TYPES,
            $type,
            static::AVAILABLE_TYPES['default']
        );
    }

    public static function available()
    {
        return array_keys(static::AVAILABLE_TYPES);
    }
}
