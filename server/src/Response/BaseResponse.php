<?php

namespace App\Response;

class BaseResponse
{
    public function __construct(public bool $success = true, public string $message = '', public int $code = 200){

    }
}