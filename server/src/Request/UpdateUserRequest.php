<?php

namespace App\Request;

use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateUserRequest extends BaseRequest
{
    public ?string $firstName;
    public ?string $lastName;
    #[Assert\Email(
        message: 'The email {{ value }} is not a valid email.',
    )]
    public ?string $userEmail;
}