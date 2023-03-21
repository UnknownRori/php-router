<?php

namespace UnknownRori\Router\Exceptions;

use RuntimeException;

class InvalidRouteConstraintException extends RuntimeException
{
    public function __construct(string $constraint)
    {
        $this->message = "Invalid constraint, get '{$constraint}' have you added it to router yet?";
    }
}
