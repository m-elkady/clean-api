<?php

declare(strict_types=1);

namespace App\Request;

interface RequestValidatedInterface
{
    public static function fromArray(array $data): self;
}