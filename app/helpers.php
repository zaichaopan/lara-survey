<?php

use App\OptionalMethod;

function optional_method($obj)
{
    return new OptionalMethod($obj);
}

function throw_exception_unless($result)
{
    if (!$result) {
        throw new \Exception;
    }
}
