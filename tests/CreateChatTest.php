<?php

namespace App\Tests;

use App\Tests\support\TestHelperTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class CreateChatTest extends WebTestCase
{
    use TestHelperTrait;

    /**
     * In this test we check current user's amount chats, then create new chat and check amount again.
     */

    public function testSomething(): void
    {
        self::loadFixtures();

        $client = $this->getAuthClient(JWTAuthTest::ADMIN_USERNAME, JWTAuthTest::ADMIN_PASSWORD);

        // Get chats amount
        $client->request('GET', '/get-chats');
        $chats = json_decode(json_decode($client->getResponse()->getContent(), true), true);
        $this->assertResponseIsSuccessful();
        $amountOld = count($chats);

        // Create new chat
        $client->request('GET', '/create-chat');
        $chatId = json_decode(json_decode($client->getResponse()->getContent(), true), true)['id'];
        $this->assertResponseIsSuccessful();

        // Check amount now
        $client->request('GET', '/get-chats');
        $chats = json_decode(json_decode($client->getResponse()->getContent(), true), true);
        $this->assertResponseIsSuccessful();
        $amountNow = count($chats);
        $this->assertEquals($amountOld + 1, $amountNow);

    }
}
