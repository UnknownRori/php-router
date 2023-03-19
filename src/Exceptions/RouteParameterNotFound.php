<?php

namespace UnknownRori\Router\Exceptions;

use RuntimeException;

class RouteParameterNotFound extends RuntimeException
{
    public function __construct(string $key, string $type)
    {
        $this->message = "Key called '{$key}' with data type '{$type}' is not found!";
    }
}
