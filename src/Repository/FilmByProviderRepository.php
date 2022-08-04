<?php

namespace App\Repository;

use App\Entity\FilmByProvider;
use App\Entity\FilmByProviderTranslation;
use App\Entity\Provider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FilmByProvider>
 *
 * @method FilmByProvider|null find($id, $lockMode = null, $lockVersion = null)
 * @method FilmByProvider|null findOneBy(array $criteria, array $orderBy = null)
 * @method FilmByProvider[]    findAll()
 * @method FilmByProvider[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FilmByProviderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FilmByProvider::class);
    }

    public function add(FilmByProvider $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FilmByProvider $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param FilmByProvider $provider
     */
    public function save(FilmByProvider $provider)
    {
        $this->_em->persist($provider);
        $this->_em->flush();
    }

    /**
     * @param FilmByProvider $provider
     */
    public function delete(FilmByProvider $provider)
    {
        $this->_em->remove($provider);
        $this->_em->flush();
    }

    public function getFilmByNoUploadedImage($providerId)
    {
        return $this->createQueryBuilder('f')
            ->select('f')
            ->join('f.provider', 'provider')
            ->where('provider.id= :id')
            ->andWhere('f.posterUploaded = 0')
            ->setParameter('id', $providerId)
            ->getQuery()
            ->getResult();
    }

}
