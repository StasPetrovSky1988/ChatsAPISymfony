<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = User::forFixtures('Admin User', 'admin@domen.com', [User::ROLE_ADMIN, User::ROLE_USER]);
        $user->setPassword($this->hasher->hashPassword($user, 'secret'));
        $manager->persist($user);

        $user = User::forFixtures('Simple User', 'user@domen.com', [User::ROLE_USER]);
        $user->setPassword($this->hasher->hashPassword($user, 'secret'));
        $manager->persist($user);

        $manager->flush();
    }
}
