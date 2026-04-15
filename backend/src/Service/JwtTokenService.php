<?php

namespace App\Service;

use App\Entity\ApiToken;
use App\Entity\User;
use App\Repository\ApiTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Security\Core\User\UserInterface;

class JwtTokenService
{
    private const REFRESH_TOKEN_TTL = 2592000; // 30 days in seconds

    public function __construct(
        private readonly JWTEncoderInterface $jwtEncoder,
        private readonly EntityManagerInterface $entityManager,
        private readonly ApiTokenRepository $apiTokenRepository,
        #[Autowire('%lexik_jwt_authentication.token_ttl%')]
        private readonly int $tokenTtl
    ) {
    }

    /**
     * Create both access and refresh tokens for a user
     * Saves tokens to database for tracking and revocation support
     *
     * @return array{access_token: string, refresh_token: string, expires_in: int, expires_at: string}
     */
    public function createTokenForUser(UserInterface $user): array
    {
        // Revoke all existing tokens for this user (one active token per user)
        $this->apiTokenRepository->revokeAllForUser($user);

        // Generate access token
        $accessTokenId = bin2hex(random_bytes(16));
        $now = time();

        $accessPayload = [
            'sub' => $user->getEmail(),
            'jti' => $accessTokenId,
            'iat' => $now,
            'exp' => $now + $this->tokenTtl,
            'type' => 'access',
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
            ],
        ];

        $accessToken = $this->jwtEncoder->encode($accessPayload);

        // Generate refresh token
        $refreshTokenId = bin2hex(random_bytes(16));

        $refreshPayload = [
            'sub' => $user->getEmail(),
            'jti' => $refreshTokenId,
            'iat' => $now,
            'exp' => $now + self::REFRESH_TOKEN_TTL,
            'type' => 'refresh',
            'user' => [
                'id' => $user->getId(),
            ],
        ];

        $refreshToken = $this->jwtEncoder->encode($refreshPayload);

        // Save tokens to database for tracking
        $apiToken = new ApiToken($user, $accessTokenId, $this->tokenTtl);
        $apiToken->setRefreshTokenId($refreshTokenId);

        $this->entityManager->persist($apiToken);
        $this->entityManager->flush();

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer',
            'expires_in' => $this->tokenTtl,
            'expires_at' => date('Y-m-d H:i:s', $now + $this->tokenTtl),
        ];
    }

    /**
     * Refresh an access token using a refresh token
     *
     * @return array{access_token: string, refresh_token: string, expires_in: int, expires_at: string}
     * @throws \Exception
     */
    public function refreshToken(string $refreshToken): array
    {
        try {
            $payload = $this->jwtEncoder->decode($refreshToken);

            if (($payload['type'] ?? '') !== 'refresh') {
                throw new \Exception('Invalid token type');
            }

            $tokenId = $payload['jti'] ?? null;
            if (!$tokenId) {
                throw new \Exception('Missing token ID');
            }

            // Verify refresh token exists in database and is valid
            $apiToken = $this->apiTokenRepository->findOneBy(['refreshTokenId' => $tokenId]);

            if (!$apiToken || !$apiToken->isValid()) {
                throw new \Exception('Refresh token not found or revoked');
            }

            $user = $apiToken->getUser();

            // Revoke old tokens and create new ones
            return $this->createTokenForUser($user);

        } catch (\Exception $e) {
            throw new \Exception('Invalid refresh token: ' . $e->getMessage());
        }
    }

    /**
     * Validate a token against the database
     */
    public function isTokenValid(string $token): bool
    {
        try {
            $payload = $this->jwtEncoder->decode($token);

            if (($payload['type'] ?? 'access') !== 'access') {
                return false;
            }

            $tokenId = $payload['jti'] ?? null;
            if (!$tokenId) {
                return false;
            }

            $apiToken = $this->apiTokenRepository->findOneByTokenId($tokenId);

            return $apiToken !== null && $apiToken->isValid();

        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Revoke a token
     */
    public function revokeToken(string $token): void
    {
        try {
            $tokenId = $this->getTokenIdFromToken($token);
            if ($tokenId) {
                $apiToken = $this->apiTokenRepository->findOneByTokenId($tokenId);
                if ($apiToken) {
                    $apiToken->revoke();
                    $this->entityManager->flush();
                }
            }
        } catch (\Exception $e) {
            // Log error but don't throw
        }
    }

    /**
     * @return array<mixed>
     */
    public function decodeToken(string $token): array
    {
        return $this->jwtEncoder->decode($token);
    }

    public function getTokenIdFromToken(string $token): ?string
    {
        try {
            $payload = $this->decodeToken($token);
            return $payload['jti'] ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
