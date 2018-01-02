<?php

namespace App;

class OptionalMethod
{
    protected $obj;

    public function __construct($obj)
    {
        $this->obj = $obj;
    }

    public function __call($method, $parameters)
    {
        if (method_exists($this->obj, $method)) {
            $this->obj->{$method}(...$parameters);
        }

        return null;
    }
}
