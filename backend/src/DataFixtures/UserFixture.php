<?php
namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setFirstName('user First name' . $i);
            $user->setLastName('user Last name' . $i);
            $user->setUserEmail('user' . $i . '@example.com');

            $manager->persist($user);
        }

        $manager->flush();
    }
}
