<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class JWTAuthTest extends WebTestCase
{
    //use TestHelperTrait;

    public const ADMIN_USERNAME = 'admin@domen.com';
    public const ADMIN_PASSWORD = 'secret';

    public function testSomething(): void
    {
        $client = static::createClient();
        $client->request('GET', '/get-chats');

        // Check protected action. It should be disabled if you are not logged in.
        $this->assertResponseStatusCodeSame(401);

        // Try to JWT login
        $client->request(
            'POST',
            '/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => self::ADMIN_USERNAME,
                'password' => self::ADMIN_PASSWORD,
            ])
        );

        // Set JWT token
        $data = json_decode($client->getResponse()->getContent(), true);
        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        // Check protected action again. It should be available
        $client->request('GET','/get-chats');
        $client->getResponse();

        $this->assertResponseIsSuccessful();
    }
}
