<?php

namespace App\Controller;

use App\Request\LoginRequest;
use App\Response\AppResponse;
use App\Service\AuthService;
use App\Service\JwtTokenService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route(path: '/auth', name: 'auth_')]
class AuthController
{
    public function __construct(
        private readonly JwtTokenService $jwtTokenService,
        private readonly AuthService $authService
    ) {
    }

    #[Route(path: '/login', name: 'login', methods: ['POST'])]
    public function login(#[CurrentUser] ?UserInterface $user): JsonResponse
    {
        // This is handled by Symfony's json_login authenticator
        // This method is called after successful authentication
        if (!$user) {
            throw new UnauthorizedHttpException('Auth', 'Authentication failed');
        }

        // Create access and refresh tokens and save to database
        $tokenData = $this->jwtTokenService->createTokenForUser($user);

        return AppResponse::success($tokenData);
    }

    #[Route(path: '/refresh', name: 'refresh', methods: ['POST'])]
    public function refresh(Request $request): JsonResponse
    {
        $refreshToken = json_decode($request->getContent(), true)['refresh_token'] ?? null;

        if (!$refreshToken) {
            return AppResponse::error('refresh_token is required', 400);
        }

        try {
            $tokenData = $this->jwtTokenService->refreshToken($refreshToken);
            return AppResponse::success($tokenData);
        } catch (\Exception $e) {
            return AppResponse::error($e->getMessage(), 401);
        }
    }

    #[Route(path: '/me', name: 'me', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
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
        // Revoke all tokens for this user
        $this->authService->logout($user);

        return AppResponse::success(['message' => 'Logged out successfully']);
    }
}
