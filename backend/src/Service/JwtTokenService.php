<?php

namespace App\Service;

use App\Entity\ApiToken;
use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class JwtTokenService
{
    public function __construct(
        private readonly JWTEncoderInterface $jwtEncoder,
        #[Autowire('%lexik_jwt_authentication.token_ttl%')]
        private readonly int $tokenTtl
    ) {
    }

    /**
     * @return array{token: string, expires_at: string}
     */
    public function createTokenForUser(User $user, ?ApiToken $apiToken = null): array
    {
        $tokenId = bin2hex(random_bytes(16));

        $payload = [
            'sub' => $user->getEmail(),
            'jti' => $tokenId,
            'iat' => time(),
            'exp' => time() + $this->tokenTtl,
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
            ],
        ];

        $token = $this->jwtEncoder->encode($payload);

        return [
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $this->tokenTtl,
            'expires_at' => date('Y-m-d H:i:s', time() + $this->tokenTtl),
        ];
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
