<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CreateChatTest extends WebTestCase
{
    /**
     * In this test we check current user's amount chats, then create new chat and check amount again.
     */
    public function testSomething(): void
    {
        $client = JWTAuthTest::getAuthClient();

        // Get chats amount
        $client->request('GET', '/get-chats');
        $chats = json_decode(json_decode($client->getResponse()->getContent(), true));
        $this->assertResponseIsSuccessful();
        $amountOld = count($chats);

        // Create new chat
        $client->request('GET', '/create-chat');
        $chatId = json_decode(json_decode($client->getResponse()->getContent(), true))->id;
        $this->assertResponseIsSuccessful();

        // Check amount now
        $client->request('GET', '/get-chats');
        $chats = json_decode(json_decode($client->getResponse()->getContent(), true));
        $this->assertResponseIsSuccessful();
        $amountNow = count($chats);
        $this->assertEquals($amountOld + 1, $amountNow);

        // Leave that chat
        $client->request('GET', '/leaveChat/' . $chatId);
        $this->assertResponseIsSuccessful();

        // Check amount again
        $client->request('GET', '/get-chats');
        $chats = json_decode(json_decode($client->getResponse()->getContent(), true));
        $this->assertResponseIsSuccessful();
        $amountNow = count($chats);
        $this->assertEquals($amountOld, $amountNow);
    }
}
