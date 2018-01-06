<?php

namespace App;

class StrategyFinder
{
    public static function get($type)
    {
        return array_get(
            static::ACCEPTABLE_TYPES,
            $type,
            static::ACCEPTABLE_TYPES['default']
        );
    }

    public static function acceptTypes()
    {
        return array_keys(static::ACCEPTABLE_TYPES);
    }
}
