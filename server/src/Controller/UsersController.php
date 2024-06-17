<?php

namespace App\Controller;

use App\Request\AddUserRequest;
use App\Request\DeleteUserRequest;
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
        $addUserRequest = $this->getRequest($request->getContent(), AddUserRequest::class);
        $response = $this->userService->add($addUserRequest);
        return $this->getResponse($response);
    }

    #[Route(path: '/user/{id}', name: 'updateUser', methods: ['PATCH', 'PUT'])]
    public function update(int $id, Request $request): Response
    {
        $updateUserRequest = $this->getRequest($request->getContent(), UpdateUserRequest::class);
        $response = $this->userService->update($id, $updateUserRequest);
        return $this->getResponse($response);
    }

    #[Route('/user/{value}/{fieldName}', defaults: ['fieldName'=> 'id'], methods: ['GET'])]
    public function read(string $value, string $fieldName): Response
    {
        $getUserRequest = $this->getRequest(json_encode([$fieldName => $value]), GetUserRequest::class);
        $response = $this->userService->get($getUserRequest);

        return $this->getResponse($response);
    }

    #[Route(path: '/user/{id}', name: 'removeUser', methods: 'DELETE')]
    public function remove(Request $request): Response
    {
        $response = $this->userService->delete($request->get('id'));
        return $this->getResponse($response);
    }

    #[Route(path: '/user', name: 'removeUser', methods: 'GET')]
    public function paginate(Request $request): Response
    {
        $start = $request->query->getInt('start');
        $perPage = $request->query->getInt('perPage', 20);
        $sortBy = $request->query->get('sort_by', 'id');
        $order = $request->query->get('order', 'asc');
        $paginateOptions = ['start' => $start, 'perPage' => $perPage, 'sortBy' => $sortBy, 'order' => $order];
        $paginateRequest = $this->getRequest(json_encode($paginateOptions), PaginateUserRequest::class);

        $response = $this->userService->paginate($paginateRequest);

        return $this->getResponse($response);
    }
}
