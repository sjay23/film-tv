<?php

namespace App\Repository;

use App\Entity\CommandTask;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CommandTask>
 *
 * @method CommandTask|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommandTask|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommandTask[]    findAll()
 * @method CommandTask[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandTaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommandTask::class);
    }

    public function add(CommandTask $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CommandTask $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param CommandTask $task
     */
    public function save(CommandTask $task)
    {
        $this->_em->persist($task);
        $this->_em->flush();
    }

    /**
     * @param CommandTask $task
     */
    public function delete(CommandTask $task)
    {
        $this->_em->remove($task);
        $this->_em->flush();
    }
}
