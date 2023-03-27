<?php

namespace UnknownRori\Router\Contracts;

interface RoutesContracts
{

    public function name(string $name): self;

    public function where(array $constraintKey): self;
    public function whereAlphaNum(string|array $placeholder): self;
    public function whereAlpha(string|array $placeholder): self;
    public function whereNumeric(string|array $placeholder): self;
}
