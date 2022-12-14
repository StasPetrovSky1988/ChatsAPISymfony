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
        $userAdmin = User::createNewUser('Admin User', 'admin@domen.com', [User::ROLE_ADMIN, User::ROLE_USER]);
        $userAdmin->setPassword($this->hasher->hashPassword($userAdmin, 'secret'));
        $manager->persist($userAdmin);

        // Create simple user
        $userSimple = User::createNewUser('Simple User', 'user@domen.com', [User::ROLE_USER]);
        $userSimple->setPassword($this->hasher->hashPassword($userSimple, 'secret'));
        $manager->persist($userSimple);

        // Create chat
        $chat1 = Chat::createNewFromUserIntent($userAdmin);
        $chat1->addParticipant($userSimple);
        $manager->persist($chat1);

        //$userSimple->connectTo($chat1);
        //$manager->persist($chat1);

        //Add messages
        $chat1->addNewMessage($userAdmin, "This is first message by User Admin");
        $chat1->addNewMessage($userAdmin, "This is second message by User Admin");
        $chat1->addNewMessage($userSimple, "This is first message by Simple User");

        //$chat1->removeParticipant($userAdmin);
        //$userSimple->leaveChat($chat1);

        // Create other Chat
        $chat2 = Chat::createNewFromUserIntent($userAdmin);
        $manager->persist($chat2);
        //$chat2->removeParticipant($userAdmin);

        $chat2 = Chat::createNewFromUserIntent($userAdmin);
        $manager->persist($chat2);
        $chat2->addNewMessage($userAdmin, "This is first message by User Admin in second chat");
        $chat2->addNewMessage($userAdmin, "This is second message by User Admin in second chat");
        //$chat2->removeParticipant($userAdmin);



        $manager->flush();
    }
}
