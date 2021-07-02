<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    private $client;

    public function setUp(): void
    {
        $this->client = static::createClient();

        $entityManager = $this->client->getContainer()->get('doctrine.orm.default_entity_manager');
        $userRepository = $entityManager->getRepository(User::class);

        // retrieve the test user
        $testUser = $userRepository->findOneByUsername('Toto');

        // simulate $testUser being logged in
        $this->client->loginUser($testUser);
    }

    public function testHomepageIsUp()
    {
        $crawler = $this->client->request('GET', '/');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('html:contains("Bienvenue sur Todo List, l\'application vous permettant de gérer l\'ensemble de vos tâches sans effort !")')->count());
    }
}
