<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * Encoder of users passwords
     *
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR');

        //Create 1 admin user and 1 standard user and 1 anonyme user

        $userAdmin = new User();

        $hash = $this->encoder->encodePassword($userAdmin, "password");

        $userAdmin->setUsername('Francis')
                ->setEmail('franimpa@yahoo.fr')
                ->setPassword($hash)
                ->setRole('ROLE_ADMIN');

        $manager->persist($userAdmin);

        $userAno = new User();

        $hash = $this->encoder->encodePassword($userAno, 'password');

        $userAno->setUsername('anonyme')
                ->setEmail('anonyme@yahoo.fr')
                ->setPassword($hash)
                ->setRole('ROLE_USER');

        $manager->persist($userAno);

        $userStd = new User();

        $hash = $this->encoder->encodePassword($userStd, 'password');

        $userStd->setUsername('Toto')
                ->setEmail('toto@gmail.com')
                ->setPassword($hash)
                ->setRole('ROLE_USER');

        $manager->persist($userStd);

        for ($i = 1; $i <= 10; ++$i) {
            $task = new Task();

            $task->setCreatedAt($faker->dateTimeThisDecade($max = 'now', $timezone = null))
                 ->setTitle($faker->sentence(2))
                 ->setContent($faker->paragraph(3))
                 ->toggle($faker->randomElement($array = [true, false]))
                 ->setAuthor($faker->randomElement($array = [$userAno, $userStd, $userAdmin]));

            $manager->persist($task);
        }

        $manager->flush();
    }
}
