<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class JWTAuthTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
//        $client->request('GET', '/get-chats');
//
//        // Check protected action. It should be disabled if you are not logged in.
//        $this->assertResponseStatusCodeSame(401);

        // Try to JWT login
        $client->request(
            'POST',
            '/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'admin@domen.com',
                'password' => 'secret',
            ])
        );

        $data = json_decode($client->getResponse()->getContent(), true);

        //
        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));
        // Check protected action again. It should be available
        $client->request(
            'POST',
            '/send-message/60',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'message' => 'Hello from client',
                'type' => 'text',
            ])
        );
        $client->getResponse();

        //var_dump($client->getResponse()->getContent(), true);

        $this->assertResponseIsSuccessful();

    }
}
