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

    public function getFilmsByActor($people)
    {
        return $this->createQueryBuilder('f')
            ->select('f')
            ->join('f.actor', 'a')
            ->where('a= :people')
            ->setParameter('people', $people)
            ->getQuery()
            ->getResult();
    }

    public function setSearchGenre($queryParameter, $qb)
    {
        $qb->andWhere('g.name = :genre_name');
        $qb->setParameter('genre_name', $queryParameter->getGenreName());
        return $qb;
    }

    public function setSearchActor($queryParameter, $qb)
    {
        $qb->andWhere('a.name LIKE :actor_name');
        $qb->setParameter('actor_name', '%' . $queryParameter->getActorName() . '%');
        return $qb;
    }

    public function setSearchDirector($queryParameter, $qb)
    {
        $qb->andWhere('d.name LIKE :director_name');
        $qb->setParameter('director_name', '%' . $queryParameter->getDirectorName() . '%');
        return $qb;
    }

    public function setSearchAudio($queryParameter, $qb)
    {
        $qb->andWhere('au.name = :lang_audio');
        $qb->setParameter('lang_audio', $queryParameter->getAudioLang());
        return $qb;
    }
    public function setSort($qb, $orderBy)
    {
        if ($orderBy == 'id_asc') {
            $qb->orderBy('f.id', 'ASC');
        }
        if ($orderBy == 'id_desc') {
            $qb->orderBy('f.id', 'DESC');
        }
        if ($orderBy == 'year_asc') {
            $qb->orderBy('f.year', 'ASC');
        }
        if ($orderBy == 'year_desc') {
            $qb->orderBy('f.year', 'DESC');
        }
        return $qb;
    }

    public function getFilmsByFilters($queryParameter)
    {
        $qb = $this->createQueryBuilder('f')
            ->select('f')
            ->join('f.actor', 'a')
            ->join('f.director', 'd')
            ->join('f.genre', 'g')
            ->join('f.audio', 'au');
        if ($queryParameter->getYear()) {
            $qb->andWhere('f.year = :year');
            $qb->setParameter('year', $queryParameter->getYear());
        }
        if ($queryParameter->getRating()) {
            $qb->andWhere('f.rating >= :rating');
            $qb->setParameter('rating', $queryParameter->getRating());
        }
        if ($queryParameter->getGenreName()) {
            $this->setSearchGenre($queryParameter, $qb);
        }
        if ($queryParameter->getActorName()) {
            $this->setSearchActor($queryParameter, $qb);
        }
        if ($queryParameter->getDirectorName()) {
            $this->setSearchDirector($queryParameter, $qb);
        }
        if ($queryParameter->getAudioLang()) {
            $this->setSearchAudio($queryParameter, $qb);
        }
        if ($orderBy = $queryParameter->getSortBy()) {
            $this->setSort($qb, $orderBy);
        }
        return $qb->getQuery()->getResult();
    }
}
