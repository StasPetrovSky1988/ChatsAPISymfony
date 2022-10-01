<?php

namespace App\Tests\support;

use App\DataFixtures\UserFixtures;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;

trait TestHelperTrait
{

    private KernelBrowser $client;

    /**
     * Manual generate token and auth client
     * @param string $email
     * @param string $password
     * @return KernelBrowser
     */
    private function getAuthClient(string $email, string $password): KernelBrowser
    {
        if (!static::$booted)  $this->client = static::createClient();

        $encoder = $this->client->getContainer()->get(JWTEncoderInterface::class);
        $token = $encoder->encode([
            'username' => $email,
            'password' => $password,
        ]);

        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $token));

        return $this->client;
    }

    /**
     * Here we load the fixtures
     */
    private function loadFixtures()
    {
        if (!static::$booted)  $this->client = static::createClient();

        $entityManager = self::$kernel->getContainer()->get('doctrine')->getManager();
        $fixtureExecutor = new ORMExecutor($entityManager, new ORMPurger($entityManager));
        $fixtureLoader = new ContainerAwareLoader(self::$kernel->getContainer());

        $hasher = self::$kernel->getContainer()->get(UserPasswordHasher::class);

        $fixtureLoader->addFixture(new UserFixtures($hasher));

        $fixtureExecutor->execute($fixtureLoader->getFixtures());
    }
}