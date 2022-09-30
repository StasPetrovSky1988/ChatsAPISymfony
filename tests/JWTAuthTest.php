<?php

namespace App\Tests;

use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class JWTAuthTest extends WebTestCase
{
    public const USERNAME = 'admin@domen.com';
    public const PASSWORD = 'secret';

    // Manual generate token and auth
    public static function getAuthClient(): KernelBrowser
    {
        $client = self::createClient();
        $encoder = $client->getContainer()->get(JWTEncoderInterface::class);

        $token = $encoder->encode([
            'username' => self::USERNAME,
            'password' => self::PASSWORD,
            'exp' => time() + 3600 // 1 hour expiration
        ]);

        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $token));

        return $client;
    }

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
                'email' => self::USERNAME,
                'password' => self::PASSWORD,
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
