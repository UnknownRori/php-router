<?php

namespace UnknownRori\Router\Exceptions;

use RuntimeException;

class RouteNotFoundException extends RuntimeException
{
    public function __construct(string $url, string $method)
    {
        $this->message = "Route with URL '{$url}' and with HTTP Method '{$method}' cannot be found!";
    }
}
