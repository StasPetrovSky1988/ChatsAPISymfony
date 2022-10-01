<?php

namespace App\Tests;

use App\Tests\support\TestHelperTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SendMessagesTest extends WebTestCase
{
    use TestHelperTrait;

    /**
     * In this test we get the first chat and count its messages, then send a new message and check the count again
     */
    public function testSomething(): void
    {
        self::loadFixtures();

        $client = $this->getAuthClient(JWTAuthTest::ADMIN_USERNAME, JWTAuthTest::ADMIN_PASSWORD);
        //$serializer = $client->getContainer()->get(SerializerInterface::class);

        // Get first chat id
        $client->request('GET', '/get-chats');
        $chats = json_decode(json_decode($client->getResponse()->getContent(), true), true);
        $this->assertResponseIsSuccessful();
        $firstChatId = $chats[0]['id'];

        // Get chat messages by id and count them
        $client->request('GET', '/get-chat/' . $firstChatId);
        $messages = json_decode(json_decode($client->getResponse()->getContent(), true), true)['messages'];
        $this->assertResponseIsSuccessful();
        $countMessages = count($messages);

        // Send new message
        $client->request(
            'POST',
            '/send-message/' . $firstChatId,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['message' => 'Hello form test'])
        );
        $this->assertResponseIsSuccessful();

        // Get chat messages by id and count them again
        $client->request('GET', '/get-chat/' . $firstChatId);
        $messages = json_decode(json_decode($client->getResponse()->getContent(), true), true)['messages'];
        $this->assertResponseIsSuccessful();
        $countMessagesNow = count($messages);

        $this->assertEquals($countMessages + 1, $countMessagesNow);
    }
}
