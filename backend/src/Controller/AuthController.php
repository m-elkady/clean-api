<?php

namespace App\Controller;

use App\Request\LoginRequest;
use App\Response\AppResponse;
use App\Service\AuthService;
use App\Service\JwtTokenService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route(path: '/auth', name: 'auth_')]
class AuthController
{
    public function __construct(
        private readonly JwtTokenService $jwtTokenService
    ) {
    }

    #[Route(path: '/login', name: 'login', methods: ['POST'])]
    public function login(#[CurrentUser] ?UserInterface $user): JsonResponse
    {
        // This is handled by Symfony's json_login authenticator
        // This method is called after successful authentication
        if (!$user) {
            return AppResponse::error('Authentication failed', 401);
        }

        $tokenData = $this->jwtTokenService->createTokenForUser($user);

        return AppResponse::success($tokenData);
    }

    #[Route(path: '/me', name: 'me', methods: ['GET'])]
    public function me(#[CurrentUser] UserInterface $user): JsonResponse
    {
        return AppResponse::success([
            'id' => $user->getId(),
            'email' => $user->getUserIdentifier(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'roles' => $user->getRoles(),
        ]);
    }

    #[Route(path: '/logout', name: 'logout', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function logout(#[CurrentUser] UserInterface $user): JsonResponse
    {
        // For JWT, logout is handled client-side by removing the token
        // This endpoint is mainly for consistency and any server-side cleanup
        return AppResponse::success(['message' => 'Logged out successfully']);
    }
}
