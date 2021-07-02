<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    private $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testUserListpageAccessIsdeniedForNonAdminUsers()
    {
        $entityManager = $this->client->getContainer()->get('doctrine.orm.default_entity_manager');
        $userRepository = $entityManager->getRepository(User::class);

        $loggedUser = $userRepository->findOneByUsername('Toto');
        $this->client->loginUser($loggedUser);

        $this->client->request('GET', '/users');

        $this->assertStringContainsString('Access Denied', $this->client->getResponse()->getContent());
    }

    public function testUserCreatepageAccessIsdeniedForNonAdminUsers()
    {
        $entityManager = $this->client->getContainer()->get('doctrine.orm.default_entity_manager');
        $userRepository = $entityManager->getRepository(User::class);

        $loggedUser = $userRepository->findOneByUsername('Toto');
        $this->client->loginUser($loggedUser);

        $this->client->request('GET', '/users/create');

        $this->assertStringContainsString('Access Denied', $this->client->getResponse()->getContent());
    }

    public function testUserEditpageAccessIsdeniedForNonAdminUsers()
    {
        $entityManager = $this->client->getContainer()->get('doctrine.orm.default_entity_manager');
        $userRepository = $entityManager->getRepository(User::class);

        $loggedUser = $userRepository->findOneByUsername('Toto');
        $this->client->loginUser($loggedUser);

        $anonymeUser = $userRepository->findOneByUsername('anonyme');
        $anonymeUserId = $anonymeUser->getId();

        $this->client->request('GET', "/users/{$anonymeUserId}/edit");

        $this->assertStringContainsString('Access Denied', $this->client->getResponse()->getContent());
    }

    public function testCreateUserByAdmin()
    {
        $entityManager = $this->client->getContainer()->get('doctrine.orm.default_entity_manager');
        $userRepository = $entityManager->getRepository(User::class);

        $loggedUser = $userRepository->findOneByUsername('Francis');
        $this->client->loginUser($loggedUser);

        $crawler = $this->client->request('GET', '/users/create');

        $this->assertStringContainsString('Créer un utilisateur', $crawler->filter('h1')->text());

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'MyUser';
        $form['user[password][first]'] = 'test';
        $form['user[password][second]'] = 'test';
        $form['user[email]'] = 'myuser@gmail.com';
        $form['user[role]'] = 'ROLE_USER';

        $this->client->submit($form);
        
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert.alert-success')->count());
        $this->assertStringContainsString("L'utilisateur a bien été ajouté.", $crawler->filter('div.alert.alert-success')->text());
    }

    public function testEditUserByAdmin()
    {
        $entityManager = $this->client->getContainer()->get('doctrine.orm.default_entity_manager');
        $userRepository = $entityManager->getRepository(User::class);

        $loggedUser = $userRepository->findOneByUsername('Francis');
        $this->client->loginUser($loggedUser);

        $userToEdit = $userRepository->findOneByUsername('anonyme');
        $userToEditId = $userToEdit->getId();

        $crawler = $this->client->request('GET', "/users/{$userToEditId}/edit");

        $this->assertStringContainsString("Modifier {$userToEdit->getUsername()}", $crawler->filter('h1')->text());

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[username]'] = 'MyUser';
        $form['user[password][first]'] = 'test';
        $form['user[password][second]'] = 'test';
        $form['user[email]'] = 'myuser@gmail.com';
        $form['user[role]'] = 'ROLE_USER';

        $this->client->submit($form);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert.alert-success')->count());
        $this->assertStringContainsString("Superbe ! L'utilisateur a bien été modifié", $crawler->filter('div.alert.alert-success')->text());
    }
}
