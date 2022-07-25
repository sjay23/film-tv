<?php

namespace App\Service;

use App\Entity\CommandTask;
use App\Repository\CommandTaskRepository;
use Doctrine\ORM\EntityManagerInterface;

class TaskService
{
    private EntityManagerInterface $entityManager;
    private CommandTaskRepository $commandTaskRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        CommandTaskRepository $commandTaskRepository
    ) {
        $this->entityManager = $entityManager;
        $this->commandTaskRepository = $commandTaskRepository;
    }

    /**
     * @param object $provider
     * @return CommandTask|null
     */
    public function getTask(object $provider): ?CommandTask
    {
        return $this->commandTaskRepository->findOneBy(['provider' => $provider]);
    }

    /**
     * @param object $film
     * @param object $task
     * @return object
     */
    public function updateTask(object $film, object $task): object
    {
        $task->setLastId($film->getMovieId());
        $task->setCountTask($task->getCountTask() + 1);
        $this->entityManager->flush();
        return $task;
    }

    /**
     * @param object $task
     */
    public function setErrorStatus(object $task): void
    {
        $task->setStatus(2);
        $this->entityManager->flush();
    }

    /**
     * @param CommandTask $task
     * @param string $description
     */
    public function setErrorDescription(CommandTask $task, string $description): void
    {
        $task->setDescriptionStatus($description);
        $this->entityManager->flush();
    }

    /**
     * @param object $task
     */
    public function setWorkStatus(object $task): void
    {
        $task->setStatus(1);
        $this->entityManager->flush();
    }

    /**
     * @param object $task
     */
    public function setNotWorkStatus(object $task): void
    {
        $task->setStatus(0);
        $this->entityManager->flush();
    }

    /**
     * @param object $task
     */
    public function updateCountTask(object $task): void
    {
        $task->setStatus(0);
        $this->entityManager->flush();
    }
}
