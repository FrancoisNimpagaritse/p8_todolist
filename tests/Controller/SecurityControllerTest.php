<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginpageIsUp()
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testLoginpageWithBadCredentials()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'Toto',
            '_password' => 'wrong_password',
        ]);

        $client->submit($form);

        $this->assertResponseRedirects('http://localhost/login');

        $crawler = $client->followRedirect();

        $this->assertSelectorExists('.alert.alert-danger');
        $this->assertSame(1, $crawler->filter('html:contains("Invalid credentials")')->count());
    }

    public function testLoginWithGoodCredentialsRedirectsToHomepage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'Toto',
            '_password' => 'password',
        ]);

        $client->submit($form);

        $this->assertResponseRedirects('http://localhost/');
        $crawler = $client->followRedirect();
        $this->assertSame(1, $crawler->filter('html:contains("Bienvenue sur Todo List, l\'application vous permettant de gérer l\'ensemble de vos tâches sans effort !")')->count());
    }

    public function testLogoutLinkRedirectsToLogin()
    {
        $client = static::createClient();

        $entityManager = $client->getContainer()->get('doctrine.orm.default_entity_manager');
        $userRepository = $entityManager->getRepository(User::class);

        // retrieve the test user
        $testUser = $userRepository->findOneByUsername('Toto');
        
        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/');
        $link = $crawler->selectLink('Se déconnecter')->link();

        $crawler = $client->click($link);

        $this->assertResponseRedirects('http://localhost/login');
        $crawler = $client->followRedirect();
        $this->assertStringContainsString('Se connecter', $client->getResponse()->getContent());
    }

    public function testLogoutRouteRedirectsToLogin()
    {
        $client = static::createClient();

        $entityManager = $client->getContainer()->get('doctrine.orm.default_entity_manager');
        $userRepository = $entityManager->getRepository(User::class);

        // retrieve the test user
        $testUser = $userRepository->findOneByUsername('Toto');

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/logout');

        $this->assertResponseRedirects('http://localhost/login');
        $crawler = $client->followRedirect();
        $this->assertStringContainsString('Se connecter', $client->getResponse()->getContent());
    }
}
