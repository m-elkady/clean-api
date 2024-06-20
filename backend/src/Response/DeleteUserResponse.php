<?php

namespace App\Response;

class DeleteUserResponse extends BaseResponse
{
    public function __construct()
    {
        parent::__construct(message: 'User deleted Successfully');
    }
}
