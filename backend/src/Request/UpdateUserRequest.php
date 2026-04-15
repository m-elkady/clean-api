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
    #[Assert\NotNull]
    #[Assert\Email(
        message: 'The email {{ value }} is not a valid email.',
    )]
    public ?string $email = null;

    #[Assert\Length(
        min: 6,
        minMessage: 'Password must be at least {{ limit }} characters long.',
    )]
    public ?string $password = null;

    public static function fromArray(array $data): self
    {
        $request = new self();
        $request->email = $data['email'] ?? null;
        $request->firstName = $data['firstName'] ?? '';
        $request->lastName = $data['lastName'] ?? '';
        $request->password = $data['password'] ?? null;

        return $request;
    }
}
