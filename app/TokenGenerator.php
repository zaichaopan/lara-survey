<?php

namespace App;

class TokenGenerator
{
    public static function generate($seed)
    {
       return str_limit(md5($seed . str_random()), 25, '');
    }
}
