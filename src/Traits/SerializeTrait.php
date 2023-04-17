<?php

namespace UnknownRori\Router\Traits;

trait SerializeTrait
{
    public function serialize(): string
    {
        return json_encode($this->toArray());
    }
}
