<?php

namespace App\Repository;

use App\Entity\FilmByProvider;
use App\Entity\FilmByProviderTranslation;
use App\Entity\Provider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
    public function __construct(ManagerRegistry $registry, ContainerInterface $container)
    {
        $this->container = $container;
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

    private function setSearchGenre($queryParameter, $qb)
    {
        $qb->andWhere('g.name = :genre_name');
        $qb->setParameter('genre_name', $queryParameter->getGenreName());
        return $qb;
    }

    private function setSearchActor($queryParameter, $qb)
    {
        $qb->andWhere('a.name LIKE :actor_name');
        $qb->setParameter('actor_name', '%' . $queryParameter->getActorName() . '%');
        return $qb;
    }

    private function setSearchDirector($queryParameter, $qb)
    {
        $qb->andWhere('d.name LIKE :director_name');
        $qb->setParameter('director_name', '%' . $queryParameter->getDirectorName() . '%');
        return $qb;
    }

    private function setSearchAudio($queryParameter, $qb)
    {
        $qb->andWhere('au.name = :lang_audio');
        $qb->setParameter('lang_audio', $queryParameter->getAudioLang());
        return $qb;
    }
    private function setSort($qb, $orderBy)
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

    private function getFilmsByWord($queryParameter)
    {
        $results = $this->container->get('sphinx')
            ->createQuery()
            ->select('id')
            ->from('plain_films')
            ->match(['title','year','description'], $queryParameter->getWord())
            ->getResults();
        return $results ;
    }

    public function getFilmsByFilters($queryParameter)
    {
        $qb = $this->createQueryBuilder('f')
            ->select('f')
            ->join('f.actor', 'a')
            ->join('f.director', 'd')
            ->join('f.genre', 'g')
            ->join('f.audio', 'au');

        if ($queryParameter->getWord()) {
            if (!$ids = $this->getFilmsByWord($queryParameter)) {
                return [];
            }
            $qb->andWhere('f.id IN (:ids)');
            $qb->setParameter('ids', $ids);
        }

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
        $films = $qb->getQuery()->getResult();
        return $films;
    }
}
