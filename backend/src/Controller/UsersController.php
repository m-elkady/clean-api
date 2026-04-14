<?php

namespace App\Controller;

use App\Request\AddUserRequest;
use App\Request\PaginateUserRequest;
use App\Request\UpdateUserRequest;
use App\Service\UserService;
use App\Response\AppResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class UsersController extends BaseController
{
    public function __construct(
        private readonly UserService $userService
    ) {
    }

    #[Route(path: '/user', name: 'addUser', methods: 'POST')]
    public function add(AddUserRequest $request): JsonResponse
    {
        try {
            $userDto = $this->userService->create($request);

            return AppResponse::created($userDto);
        } catch (UnprocessableEntityHttpException $e) {
            return AppResponse::error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    #[Route(path: '/user/{id}', name: 'updateUser', methods: ['PATCH', 'PUT'])]
    public function update(int $id, UpdateUserRequest $request): JsonResponse
    {
        try {
            $userDto = $this->userService->update($id, $request);

            return AppResponse::success($userDto);
        } catch (UnprocessableEntityHttpException $e) {
            return AppResponse::error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (NotFoundHttpException $e) {
            return AppResponse::notFound($e->getMessage());
        }
    }

    #[Route('/user/{id}', methods: ['GET'])]
    public function getById(int $id): JsonResponse
    {
        $userDto = $this->userService->findOneBy((string)$id, 'id');

        if (!$userDto) {
            return AppResponse::notFound('User not found');
        }

        return AppResponse::success($userDto);
    }

    #[Route('/user/by/{fieldName}/{value}', defaults: ['fieldName' => 'id'], methods: ['GET'])]
    public function getByField(string $value, string $fieldName = 'id'): JsonResponse
    {
        try{
            $userDto = $this->userService->findOneBy($value, $fieldName);
            
            return AppResponse::success($userDto);
        } catch (NotFoundHttpException $e) {
            return AppResponse::notFound('User not found');
        }

    }

    #[Route('/user/{id}', methods: ['DELETE'], name: 'removeUser')]
    public function delete(int $id): JsonResponse
    {
        $this->userService->delete($id);

        return AppResponse::noContent();
    }

    #[Route(path: '/user', name: 'paginateUsers', methods: 'GET')]
    public function paginate(PaginateUserRequest $request): JsonResponse
    {
        $result = $this->userService->paginate($request);

        return AppResponse::success($result);
    }
}
