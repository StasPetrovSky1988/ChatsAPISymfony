<?php

namespace App\DataFixtures;

use App\Entity\Chat;
use App\Entity\Message;
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
        // Create admin user
        $userAdmin = User::forFixtures('Admin User', 'admin@domen.com', [User::ROLE_ADMIN, User::ROLE_USER]);
        $userAdmin->setPassword($this->hasher->hashPassword($userAdmin, 'secret'));
        $manager->persist($userAdmin);

        // Create simple user
        $userSimple = User::forFixtures('Simple User', 'user@domen.com', [User::ROLE_USER]);
        $userSimple->setPassword($this->hasher->hashPassword($userSimple, 'secret'));
        $manager->persist($userSimple);

        $chat1 = new Chat();

        // Connect Admin user
        $chat1->addUser($userAdmin);

        // Connect Simple user
        $chat1->addUser($userSimple);
        $manager->persist($chat1);

        //Add messages
        $message = Message::newMessage($userAdmin, $chat1, "This is first message by User Admin");
        $manager->persist($message);

        $message = Message::newMessage($userAdmin, $chat1, "This is second message by User Admin");
        $manager->persist($message);

        $message = Message::newMessage($userSimple, $chat1, "This is first message by Simple User");
        $manager->persist($message);


        // Create other Chat
        $chat2 = new Chat();
        $chat2->addUser($userAdmin);
        $manager->persist($chat2);

        $manager->flush();
    }
}
