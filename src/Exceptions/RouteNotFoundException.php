<?php

namespace UnknownRori\Router\Exceptions;

use RuntimeException;

class RouteNotFoundException extends RuntimeException
{
    public function __construct(string $url = '', string $method = '', string $key = null)
    {
        if (!is_null($key)) {
            $this->message = "Route with URL '{$url}' and with HTTP Method '{$method}' cannot be found!";
        } else {
            $this->message = "Cannot find route that has name of '{$key}'";
        }
    }
}
