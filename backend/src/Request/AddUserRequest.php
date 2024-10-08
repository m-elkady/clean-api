<?php

namespace App\Request;

use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints as Assert;

class AddUserRequest extends BaseRequest
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    public string $firstName;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    public string $lastName;

    #[Assert\NotBlank]
    #[Assert\Email(
        message: 'The email {{ value }} is not a valid email.',
    )]
    public string $userEmail;


    public static function fromJson(string $json, SerializerInterface $serializer): self
    {
        return $serializer->deserialize($json, self::class, 'json');
    }
}