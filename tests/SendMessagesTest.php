<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SendMessagesTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = JWTAuthTest::getAuthClient();
    }
}
