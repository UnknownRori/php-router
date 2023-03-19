<?php

namespace UnknownRori\Router\Exceptions;

use RuntimeException;

class BadHttpMethodException extends RuntimeException
{
    public function __construct(string $method)
    {
        $this->message = "HTTP method is invalid, expected [GET, POST, PATCH, DELETE] and it passed {$method}";
    }
}
