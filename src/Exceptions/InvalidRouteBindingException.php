<?php

namespace UnknownRori\Router\Exceptions;

use RuntimeException;
use UnknownRori\Router\RouteCollection;

class InvalidRouteBindingException extends RuntimeException
{
    public function __construct(string $bind)
    {

        $this->message = "Invalid binding, " . RouteCollection::class . " did not expect this kind of bind : {$bind}";
    }
}
