<?php

namespace App\DataFixtures;

use App\Entity\CommandTask;
use App\Repository\CommandTaskRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CreateNameTaskFixture extends Fixture
{
    private CommandTaskRepository $taskRepository;

    /**
     * run console - php bin/console doctrine:fixtures:load --append --group=CreateNameTaskFixture
     */

    public function __construct(
        CommandTaskRepository $taskRepository
    ) {
        $this->taskRepository = $taskRepository;
    }

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $task = new CommandTask(
            'addFilmByParser_' . rand(1, 100)
        );

        try {
            $this->taskRepository->save($task);
            $manager->persist($task);
            $manager->flush();
        } catch (\Exception $exception) {
            dd($exception->getMessage());
        }
    }
}
