<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210702155350 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Link all existing tasks to user nammed anonyme';
    }

    public function up(Schema $schema): void
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $anonymeUser = $em->getRepository(User::class)->findOneBy(['username' => 'anonyme']);

        $anonymeUser->getUsername();

        $anonymeTasks = $em->getRepository(Task::class)->findBy(['author' => null]);

        /** @var Task $task */
        foreach ($anonymeTasks as $task) {
            $task->setAuthor($anonymeUser);
        }

        $em->flush();
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $em = $this->container->get('doctrine.orm.entity_manager');
        $anonymeUser = $em->getRepository(User::class)->findOneBy(['username' => 'anonyme']);
        $anonymeTasks = $em->getRepository(Task::class)->findBy(['author' => $anonymeUser]);

        /** @var Task $task */
        foreach ($anonymeTasks as $task) {
            $task->setAuthor(null);
        }

        $em->flush();
    }
}
