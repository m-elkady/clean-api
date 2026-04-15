<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $users = [
            [
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => 'john@example.com',
                'roles' => ['ROLE_ADMIN'],
            ],
            [
                'firstName' => 'Jane',
                'lastName' => 'Smith',
                'email' => 'jane@example.com',
                'roles' => ['ROLE_USER'],
            ],
            [
                'firstName' => 'Bob',
                'lastName' => 'Johnson',
                'email' => 'bob@example.com',
                'roles' => ['ROLE_USER'],
            ],
            [
                'firstName' => 'Alice',
                'lastName' => 'Williams',
                'email' => 'alice@example.com',
                'roles' => ['ROLE_USER'],
            ],
            [
                'firstName' => 'Charlie',
                'lastName' => 'Brown',
                'email' => 'charlie@example.com',
                'roles' => ['ROLE_USER'],
            ],
        ];

        foreach ($users as $userData) {
            $user = new User();
            $user->setFirstName($userData['firstName']);
            $user->setLastName($userData['lastName']);
            $user->setEmail($userData['email']);
            $user->setRoles($userData['roles']);

            // Hash password "123456" for all users
            $hashedPassword = $this->passwordHasher->hashPassword($user, '123456');
            $user->setPassword($hashedPassword);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
