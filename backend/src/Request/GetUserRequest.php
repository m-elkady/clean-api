<?php

namespace App\Request;

use Symfony\Component\Serializer\SerializerInterface;

class GetUserRequest extends BaseRequest
{
    public ?int $id;
    public ?string $firstName;
    public ?string $lastName;
    public ?string $userEmail;

    public static function fromJson(string $json, SerializerInterface $serializer): self
    {
        return $serializer->deserialize($json, self::class, 'json');
    }
}