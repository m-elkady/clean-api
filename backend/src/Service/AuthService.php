<?php

namespace App\Service;

use App\Entity\User;
use App\Factory\RefreshTokenFactory;
use App\Repository\RefreshTokenRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly RefreshTokenFactory $refreshTokenFactory,
        private readonly RefreshTokenRepository $refreshTokenRepository,
        private readonly EntityManagerInterface $entityManager
    ) {}

    /**
     * Authenticate user with email and password
     * @throws UnauthorizedHttpException
     */
    public function login(string $email, string $password): array
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            throw new UnauthorizedHttpException('Auth', 'Invalid credentials');
        }

        if (!$this->passwordHasher->isPasswordValid($user, $password)) {
            throw new UnauthorizedHttpException('Auth', 'Invalid credentials');
        }

        // Revoke all existing refresh tokens for this user (one active session policy)
        $this->refreshTokenRepository->revokeAllForUser($user);

        // Create new refresh token using factory
        $refreshToken = $this->refreshTokenFactory->create($user);
        $this->refreshTokenRepository->save($refreshToken);

        return [
            'user' => $user,
            'refresh_token' => $refreshToken->toString(),
        ];
    }

    public function refreshAccessToken(string $refreshToken): array
    {
        // Extract selector (first part before the dot)
        $selector = explode('.', $refreshToken)[0];

        $tokenEntity = $this->refreshTokenRepository->findOneBySelector($selector);

        if (!$tokenEntity || !$tokenEntity->isValidToken() || !$tokenEntity->validate($refreshToken)) {
            throw new UnauthorizedHttpException('Auth', 'Invalid refresh token');
        }

        $user = $tokenEntity->getUser();

        // Revoke old refresh token
        $tokenEntity->revoke();

        // Revoke all existing tokens (one session policy)
        $this->refreshTokenRepository->revokeAllForUser($user);
        $this->entityManager->flush();

        // Create new refresh token
        $newRefreshToken = $this->refreshTokenFactory->create($user);
        $this->refreshTokenRepository->save($newRefreshToken);

        return [
            'refresh_token' => $newRefreshToken->toString(),
            'user' => $user,
        ];
    }

    public function logout(User $user): void
    {
        $this->refreshTokenRepository->revokeAllForUser($user);
    }
}
