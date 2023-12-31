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
        $task = $this->commandTaskRepository->findOneBy(['provider' => $provider]);
        $this->entityManager->refresh($task);
        return $task;
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
     * @param string $description
     */
    public function setErrorStatus(object $task, string $description): void
    {
        $task->setStatus(CommandTask::STATUS_ERROR);
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
        $task->setCountTask(0);
        $this->entityManager->flush();
    }
}
