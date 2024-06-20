<?php

namespace App\Controller;

use App\Request\AddUserRequest;
use App\Request\GetUserRequest;
use App\Request\PaginateUserRequest;
use App\Request\UpdateUserRequest;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UsersController extends BaseController
{
    public function __construct(private readonly UserService $userService)
    {
    }

    #[Route(path: '/user', name: 'addUser', methods: 'POST')]
    public function add(Request $request): Response
    {
        $addUserRequest = AddUserRequest::fromJson($request->getContent(), $this->serializer);
        $response = $this->userService->add($addUserRequest);
        return $this->getResponse($response);
    }

    #[Route(path: '/user/{id}', name: 'updateUser', methods: ['PATCH', 'PUT'])]
    public function update(int $id, Request $request): Response
    {
        $updateUserRequest = UpdateUserRequest::fromJson($request->getContent(), $this->serializer);;
        $response = $this->userService->update($id, $updateUserRequest);
        return $this->getResponse($response);
    }

    #[Route('/user/{value}/{fieldName}', defaults: ['fieldName' => 'id'], methods: ['GET'])]
    public function read(string $value, string $fieldName = 'id'): Response
    {
        $getUserRequest = GetUserRequest::fromJson(json_encode([$fieldName => $value]), $this->serializer);
        $response = $this->userService->get($getUserRequest);

        return $this->getResponse($response);
    }

    #[Route('/user/{id}', methods: ['DELETE'], name: 'removeUser')]
    public function delete(int $id): Response
    {
        $response = $this->userService->delete($id);
        return $this->getResponse($response);
    }

    #[Route(path: '/user', name: 'paginateUsers', methods: 'GET')]
    public function paginate(Request $request): Response
    {
        $paginateRequest = PaginateUserRequest::fromRequest($request, $this->serializer);
        $response = $this->userService->paginate($paginateRequest);

        return $this->getResponse($response);
    }
}
