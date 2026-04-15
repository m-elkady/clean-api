<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateUserRequest extends BaseRequest
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    public string $firstName = '';

    #[Assert\NotBlank]
    #[Assert\NotNull]
    public string $lastName = '';

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Email(
        message: 'The email {{ value }} is not a valid email.',
    )]
    public ?string $email = null;
}
