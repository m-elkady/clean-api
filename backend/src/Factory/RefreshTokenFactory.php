<?php

namespace App\Factory;

use App\Entity\RefreshToken;
use App\Entity\User;

class RefreshTokenFactory
{
    private const REFRESH_TOKEN_TTL = 2592000; // 30 days in seconds

    public function create(User $user): RefreshToken
    {
        // Generate selector (public identifier for DB lookup) - 24 chars
        $selector = bin2hex(random_bytes(12));

        // Generate validator (secret part, not stored in plain) - 64 chars
        $validator = bin2hex(random_bytes(32));
        $hashedValidator = hash('sha256', $validator);

        $expiresAt = new \DateTimeImmutable('+' . self::REFRESH_TOKEN_TTL . ' seconds');

        $token = new RefreshToken($user, $selector, $hashedValidator, $expiresAt);
        $token->setPlainValidator($validator);

        return $token;
    }
}
