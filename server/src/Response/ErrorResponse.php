<?php

namespace App\Response;

class ErrorResponse extends BaseResponse
{
    public function __construct(public string $message, public int $code, public array $errors = [])
    {
        parent::__construct(false, $message, $code);
    }
}