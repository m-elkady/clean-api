<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateUserRequest extends BaseRequest implements RequestValidatedInterface
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    public string $firstName = '';

    #[Assert\NotBlank]
    #[Assert\NotNull]
    public string $lastName = '';

    #[Assert\NotBlank]
    #[Assert\Email(
        message: 'The email {{ value }} is not a valid email.',
    )]
    public ?string $userEmail = null;

    public static function fromArray(array $data): self
    {
        $request = new self();
        $request->userEmail = $data['userEmail'] ?? null;
        $request->firstName = $data['firstName'] ?? '';
        $request->lastName = $data['lastName'] ?? '';

        return $request;
    }
}
