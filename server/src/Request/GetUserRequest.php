<?php

namespace App\Request;

class GetUserRequest extends BaseRequest
{
    public ?int $id;
    public ?string $firstName;
    public ?string $lastName;
    public ?string $userEmail;
}