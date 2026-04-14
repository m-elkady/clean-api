<?php

namespace App\Service;

use App\Dto\UserData;
use App\Entity\User;
use App\Request\AddUserRequest;
use App\Request\PaginateUserRequest;
use App\Request\UpdateUserRequest;
use App\Repository\UserRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserService
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {
    }

    public function create(AddUserRequest $request): UserData
    {
        $user = new User();
        $user->setFirstName($request->firstName);
        $user->setLastName($request->lastName);
        $user->setUserEmail($request->userEmail);

        $this->userRepository->add($user);

        return UserData::fromEntity($user);
    }

    public function update(int $id, UpdateUserRequest $request): UserData
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        $user->setFirstName($request->firstName);
        $user->setLastName($request->lastName);
        $user->setUserEmail($request->userEmail ?? $user->getUserEmail());

        $this->userRepository->getEntityManager()->flush();

        return UserData::fromEntity($user);
    }

    public function delete(int $id): void
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        $this->userRepository->remove($user);
    }

    public function findOneBy(string $value, string $field = 'id'): ?UserData
    {
        $user = $this->userRepository->findOneBy([$field => $value]);

        if (!$user) {
            return null;
        }

        return UserData::fromEntity($user);
    }

    public function paginate(PaginateUserRequest $request): array
    {
        $paginator = $this->userRepository->findAll(
            [
                'page' => $request->page,
                'perPage' => $request->perPage,
                'sortBy' => $request->sortBy,
                'order' => $request->order,
            ],
            $request->getQueryOptions()
        );

        $users = [];
        
        foreach ($paginator as $user) {
            $users[] = UserData::fromEntity($user);
        }

        return [
            'users' => $users,
            'count' => count($paginator),
            'currentPage' => $request->page,
            'limit' => $request->perPage,
        ];
    }
}
