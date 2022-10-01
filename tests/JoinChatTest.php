<?php

namespace App\Tests;

use App\Tests\support\TestHelperTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class JoinChatTest extends WebTestCase
{
    use TestHelperTrait;

    public function testSomething(): void
    {
        self::loadFixtures();

        $client = $this->getAuthClient(JWTAuthTest::ADMIN_USERNAME, JWTAuthTest::ADMIN_PASSWORD);

        // Get first chat
        $client->request('GET', '/get-chats');
        $chats = json_decode(json_decode($client->getResponse()->getContent(), true));
        $this->assertResponseIsSuccessful();

        $firstChatId = $chats[0]->id;
        $countChatsStart = count($chats);

        // Leave that chat
        $client->request('GET', '/leave-chat/' . $firstChatId);
        $this->assertResponseIsSuccessful();

        // Get first chat again
        $client->request('GET', '/get-chats');
        $chats = json_decode(json_decode($client->getResponse()->getContent(), true));
        $this->assertResponseIsSuccessful();
        $chatCountAfterLeave =  count($chats);

        $this->assertNotEquals($countChatsStart, $chatCountAfterLeave);

        // Back join
        $client->request('GET', '/join-chat/' . $firstChatId);
        $this->assertResponseIsSuccessful();

        // Check first chat connected
        $client->request('GET', '/get-chats');
        $chats = json_decode(json_decode($client->getResponse()->getContent(), true));
        $chatCountAgain = count($chats);

        $this->assertResponseIsSuccessful();
        $firstChatAgainId = $chats[0]->id;
        $this->assertEquals($countChatsStart, $chatCountAgain);

    }
}
