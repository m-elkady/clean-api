<?php

namespace App\Controller;

use App\Entity\User;
use App\Request\LoginRequest;
use App\Request\RefreshRequest;
use App\Response\AppResponse;
use App\Service\AuthService;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/auth', name: 'auth_')]
class AuthController
{
    public function __construct(
        private readonly AuthService              $authService,
        private readonly JWTTokenManagerInterface $jwtManager
    )
    {
    }

    #[Route(path: '/login', name: 'login', methods: ['POST'])]
    public function login(LoginRequest $request): JsonResponse
    {
        // Manual authentication via AuthService
        $result = $this->authService->login($request->email, $request->password);

        $user = $result['user'];
        $refreshToken = $result['refresh_token'];

        $accessToken = $this->jwtManager->create($user);

        return AppResponse::success([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer',
            'expires_in' => 3600,
        ]);
    }

    #[Route(path: '/refresh', name: 'refresh', methods: ['POST'])]
    public function refresh(RefreshRequest $request): JsonResponse
    {
        $result = $this->authService->refreshAccessToken($request->refresh_token);
        $user = $result['user'];
        $newRefreshToken = $result['refresh_token'];

        // Generate new access token
        $accessToken = $this->jwtManager->create($user);

        return AppResponse::success([
            'access_token' => $accessToken,
            'refresh_token' => $newRefreshToken,
            'token_type' => 'Bearer',
            'expires_in' => 3600,
        ]);

    }

    #[Route(path: '/me', name: 'me', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function me(#[CurrentUser] User $user): JsonResponse
    {
        return AppResponse::success([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'roles' => $user->getRoles(),
        ]);
    }

    #[Route(path: '/logout', name: 'logout', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function logout(#[CurrentUser] User $user): JsonResponse
    {
        $this->authService->logout($user);

        return AppResponse::success(['message' => 'Logged out successfully']);
    }
}
