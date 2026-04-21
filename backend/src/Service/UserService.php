<?php

namespace App\Service;

use App\Dto\UserData;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Request\AddUserRequest;
use App\Request\PaginateUserRequest;
use App\Request\UpdateUserRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher
    )
    {
    }

    public function create(AddUserRequest $request): UserData
    {
        // Check if email already exists
        $existingUser = $this->userRepository->findOneBy(['email' => $request->email]);

        if ($existingUser !== null) {
            throw new UnprocessableEntityHttpException('Email already exists');
        }

        $user = new User();
        $user->setFirstName($request->firstName);
        $user->setLastName($request->lastName);
        $user->setEmail($request->email);

        // Hash and set the password
        $hashedPassword = $this->passwordHasher->hashPassword($user, $request->password);
        $user->setPassword($hashedPassword);

        $this->userRepository->add($user);

        return UserData::fromEntity($user);
    }

    public function update(int $id, UpdateUserRequest $request): UserData
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        // Check if email already exists (excluding current user)
        if ($request->email !== null) {
            $existingUser = $this->userRepository->findByEmailExcludingId($request->email, $id);

            if ($existingUser) {
                throw new UnprocessableEntityHttpException('Email already exists');
            }
        }

        $user->setFirstName($request->firstName);
        $user->setLastName($request->lastName);
        $user->setEmail($request->email);

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
            throw new NotFoundHttpException('User not found');
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
            'perPage' => $request->perPage,
        ];
    }
}
