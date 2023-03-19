<?php

namespace UnknownRori\Router\Contracts;

interface FromArray
{
    public static function fromArray(array $deserialize): self;
}
