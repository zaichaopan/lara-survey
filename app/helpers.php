<?php

use App\OptionalMethod;

if (!function_exists('optional_method')) {
    function optional_method($obj)
    {
        return new OptionalMethod($obj);
    }
}

if (!function_exists('throw_exception_unless')) {
    function throw_exception_unless($result, $exceptionClass = null)
    {
        $exceptionClass = $exceptionClass ?? \Exception::class;

        if (!$result) {
            throw new $exceptionClass;
        }
    }
}

if (!function_exists('ucfirst_str_replace')) {
    function ucfirst_str_replace($search, $replace, $string)
    {
        return ucfirst(str_replace($search, $replace, $string));
    }
}
