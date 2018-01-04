<?php

use App\OptionalMethod;

function optional_method($obj)
{
    return new OptionalMethod($obj);
}

function throw_exception_unless($result, $exceptionClass = null)
{
    $exceptionClass = $exceptionClass ?? \Exception::class;
    if (!$result) {
        throw new $exceptionClass;
    }
}
