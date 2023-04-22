<?php

namespace UnknownRori\Router\Exceptions;

use RuntimeException;

class MiddlewareNotFoundException extends RuntimeException
{
    public function __construct(string $key)
    {
        $this->message = "Middleware with key {$key} is not found, have you defined yet?";
    }
}
