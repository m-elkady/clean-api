<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\ApiTokenRepository;
use App\Repository\UserRepository;

class AuthService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly ApiTokenRepository $apiTokenRepository
    ) {
    }

    public function logout(User $user): void
    {
        $this->apiTokenRepository->revokeAllForUser($user);
    }
}
