<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    private $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testTaskListpageIsUpForAuthenticatedUsers()
    {
        $entityManager = $this->client->getContainer()->get('doctrine.orm.default_entity_manager');
        $userRepository = $entityManager->getRepository(User::class);

        $loggedUser = $userRepository->findOneByUsername('Francis');

        $this->client->loginUser($loggedUser);

        $this->client->request('GET', '/tasks');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateTaskRedirectsToTaskListpage()
    {
        $entityManager = $this->client->getContainer()->get('doctrine.orm.default_entity_manager');
        $userRepository = $entityManager->getRepository(User::class);

        $loggedUser = $userRepository->findOneByUsername('Francis');

        $this->client->loginUser($loggedUser);

        $crawler = $this->client->request('GET', '/tasks/create');
        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => 'Titre de test fonctionnel 1',
            'task[content]' => 'Contenu de test fonctionnel 1',
        ]);

        $this->client->submit($form);

        $this->assertResponseRedirects('/tasks');
        $crawler = $this->client->followRedirect();
        $this->assertSame(1, $crawler->filter('html:contains("Superbe ! La tâche a été bien été ajoutée.")')->count());
    }

    public function testEditTaskRedirectsToTaskListpage()
    {
        $entityManager = $this->client->getContainer()->get('doctrine.orm.default_entity_manager');
        $userRepository = $entityManager->getRepository(User::class);

        $loggedUser = $userRepository->findOneByUsername('Toto');
        $this->client->loginUser($loggedUser);
        $taskRepository = $entityManager->getRepository(Task::class);
        
        $oneTask = $taskRepository->findBy(['author' => $loggedUser], [], 1, null);
        $taskId = $oneTask[0]->getId();

        $crawler = $this->client->request('GET', "tasks/{$taskId}/edit");

        $form = $crawler->selectButton('Modifier')->form([
            'task[title]' => 'Titre de test fonctionnel 1',
            'task[content]' => 'Contenu de test fonctionnel 1',
        ]);

        $this->client->submit($form);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->assertResponseRedirects('/tasks');

        $crawler = $this->client->followRedirect();

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert.alert-success')->count());
        $this->assertStringContainsString('La tâche a bien été modifiée.', $this->client->getResponse()->getContent());
    }

    public function testToggleTaskWorksForOwner()
    {
        $entityManager = $this->client->getContainer()->get('doctrine.orm.default_entity_manager');
        $userRepository = $entityManager->getRepository(User::class);

        $loggedUser = $userRepository->findOneByUsername('Toto');
        $this->client->loginUser($loggedUser);

        $taskRepository = $entityManager->getRepository(Task::class);
        $oneTask = $taskRepository->findBy(['author' => $loggedUser], [], 1, null);
        $taskId = $oneTask[0]->getId();

        $this->client->request('GET', "/tasks/{$taskId}/toggle");

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());
    }

    /*  //tester si le lien d'edition conduit vers la page de modification

      //tester si l'autorisation marche : cas où on est pas auteur de la tache
      public  function testLinkToEditWorksIfTaskOwner()
      {
          $crawler = $this->client->request('GET', '/tasks');
          $link = $crawler->selectLink('')->link();
          $crawler = $this->client->click($link);
      }

       //tester si l'autorisation marche : cas où on est admin et owner est anonyme
       public  function testLinkToEditWorksIfTaskOwnerIsAnynymeAndUserHasRoleAdmin()
       {

       } */

    public function testOwnerAndLoggedUserCanDeleteHisTask()
    {
        $entityManager = $this->client->getContainer()->get('doctrine.orm.default_entity_manager');
        $userRepository = $entityManager->getRepository(User::class);

        $loggedUser = $userRepository->findOneByUsername('Toto');
        $this->client->loginUser($loggedUser);

        $taskRepository = $entityManager->getRepository(Task::class);
        $oneTask = $taskRepository->findBy(['author' => $loggedUser], [], 1, null);
        $taskId = $oneTask[0]->getId();

        $this->client->request('GET', "/tasks/{$taskId}/delete");

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());
    }

    public function testDeleteForbiddenToUserWithRoleUserOnAnomnymeUserTask()
    {
        $entityManager = $this->client->getContainer()->get('doctrine.orm.default_entity_manager');
        $userRepository = $entityManager->getRepository(User::class);

        $loggedUser = $userRepository->findOneByUsername('Toto');
        $this->client->loginUser($loggedUser);

        $anonymeUser = $userRepository->findOneByUsername('anonyme');

        $taskRepository = $entityManager->getRepository(Task::class);
        $oneAnonymeTask = $taskRepository->findBy(['author' => $anonymeUser], [], 1, null);
        $taskId = $oneAnonymeTask[0]->getId();

        $this->client->request('GET', "/tasks/{$taskId}/delete");

        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testDeleteAllowedToUserWithRoleAdminOnAnomnymeUserTask()
    {
        $entityManager = $this->client->getContainer()->get('doctrine.orm.default_entity_manager');
        $userRepository = $entityManager->getRepository(User::class);

        $loggedUser = $userRepository->findOneByUsername('Francis');
        $this->client->loginUser($loggedUser);

        $anonymeUser = $userRepository->findOneByUsername('anonyme');

        $taskRepository = $entityManager->getRepository(Task::class);
        $oneAnonymeTask = $taskRepository->findBy(['author' => $anonymeUser], [], 1, null);
        $taskId = $oneAnonymeTask[0]->getId();

        $this->client->request('GET', "/tasks/{$taskId}/delete");

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());
    }
}
