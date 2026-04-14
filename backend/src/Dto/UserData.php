<?php

namespace App\Dto;

class UserData
{
    public function __construct(
        public int $id,
        public string $firstName,
        public string $lastName,
        public string $userEmail,
        public string $createdAt
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['firstName'],
            $data['lastName'],
            $data['userEmail'],
            $data['createdAt'] ?? ''
        );
    }

    public static function fromEntity(object $entity): self
    {
        return new self(
            $entity->getId(),
            $entity->getFirstName(),
            $entity->getLastName(),
            $entity->getUserEmail(),
            $entity->getCreatedAt()
        );
    }
}
