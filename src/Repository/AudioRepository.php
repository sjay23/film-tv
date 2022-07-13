<?php

namespace App\Repository;

use App\Entity\Audio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Audio>
 *
 * @method Audio|null find($id, $lockMode = null, $lockVersion = null)
 * @method Audio|null findOneBy(array $criteria, array $orderBy = null)
 * @method Audio[]    findAll()
 * @method Audio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AudioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Audio::class);
    }

    /**
     * @param Audio $audio
     */
    public function save(Audio $audio)
    {
        $this->_em->persist($audio);
        $this->_em->flush();
    }


    /**
     * @param Audio $audio
     */
    public function delete(Audio $audio)
    {
        $this->_em->remove($audio);
        $this->_em->flush();
    }
}
