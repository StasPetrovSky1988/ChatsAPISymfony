<?php

namespace App\Tests;

use App\Tests\support\TestHelperTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploadTest extends WebTestCase
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

        $client->request(
            'POST',
            '/send-message/' . $firstChatId,
            [],
            ['data' => $this->prepareFile()],
            ['Content-Type' => 'multipart/formdata'],
            json_encode([
                'qewr' => 'asdf',
            ])
        );
    }

    private function prepareFile()
    {
        // Create file
        $file = tempnam(sys_get_temp_dir(), 'upl');
        $file = fopen()

        file_put_contents($file, 'This is some random text.');

        return new UploadedFile(
            $file,
            'emptyfile.txt'
        );
    }
}
