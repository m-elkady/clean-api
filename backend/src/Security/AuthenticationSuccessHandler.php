<?php

namespace App\Security;

use App\Service\JwtTokenService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(
        private readonly JwtTokenService $jwtTokenService
    ) {
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): JsonResponse
    {
        $user = $token->getUser();

        // Create access and refresh tokens and save to database
        $tokenData = $this->jwtTokenService->createTokenForUser($user);

        return new JsonResponse([
            'success' => true,
            'data' => $tokenData,
        ]);
    }
}
