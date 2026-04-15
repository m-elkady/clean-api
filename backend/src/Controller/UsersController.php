<?php

namespace App\Controller;

use App\Request\AddUserRequest;
use App\Request\PaginateUserRequest;
use App\Request\UpdateUserRequest;
use App\Response\AppResponse;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/user', name: 'user_')]
class UsersController extends BaseController
{
    public function __construct(
        private readonly UserService $userService
    )
    {
    }

    #[Route(path: '/', name: 'addUser', methods: 'POST')]
    public function add(AddUserRequest $request): JsonResponse
    {
        $userDto = $this->userService->create($request);

        return AppResponse::created($userDto);
    }

    #[Route(path: '/{id}', name: 'updateUser', methods: ['PATCH', 'PUT'])]
    public function update(int $id, UpdateUserRequest $request): JsonResponse
    {
        $userDto = $this->userService->update($id, $request);

        return AppResponse::success($userDto);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function getById(int $id): JsonResponse
    {
        $userDto = $this->userService->findOneBy((string)$id);

        return AppResponse::success($userDto);
    }

    #[Route('/by/{fieldName}/{value}', defaults: ['fieldName' => 'id'], methods: ['GET'])]
    public function getByField(string $value, string $fieldName = 'id'): JsonResponse
    {
        $userDto = $this->userService->findOneBy($value, $fieldName);

        return AppResponse::success($userDto);
    }

    #[Route('/{id}', name: 'removeUser', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $this->userService->delete($id);

        return AppResponse::noContent();
    }

    #[Route(path: '/', name: 'paginateUsers', methods: 'GET')]
    public function paginate(PaginateUserRequest $request): JsonResponse
    {
        $result = $this->userService->paginate($request);

        return AppResponse::success($result);
    }
}
