<?php

namespace App\Service;

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
     * @return object
     */
    public function getTask(object $provider): object
    {
        $task=$this->commandTaskRepository->findOneBy(['provider'=> $provider]);
        return $task;

    }

    /**
     * @param object $film
     * @param object $task
     * @return object
     */
    public function updateTask(object $film,object $task): object
    {
        $task->setLastId($film->getMovieId());
        $task->setCountTask( $task->getCountTask() + 1);
        $this->entityManager->flush();
        return $task;

    }

    /**
     * @param object $task
     */
    function setErrorStatus(object $task)
    {
        $task->setStatus(2);
        $this->entityManager->flush();
    }

    /**
     * @param object $task
     */
    function setWorkStatus(object $task)
    {
        $task->setStatus(1);
        $this->entityManager->flush();
    }

    /**
     * @param object $task
     */
    function setNotWorkStatus(object $task)
    {
        $task->setStatus(0);
        $this->entityManager->flush();
    }

}
