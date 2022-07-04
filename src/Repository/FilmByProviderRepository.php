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

//    /**
//     * @return FilmByProvider[] Returns an array of FilmByProvider objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?FilmByProvider
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }


        public function test(Provider $provider)
        {
            /**
             * Как добавить перевод пример
             */
            $filmsProvider = new FilmByProvider();
            $filmsProvider->translate('en')->setTitle('my title in en1');
            $filmsProvider->translate('ru')->setTitle('my title in ru');
            $filmsProvider->translate('en')->setDescription('my content in en1');
            $filmsProvider->translate('ru')->setDescription('my content in ru');
            $filmsProvider->setProvider($provider);
            $filmsProvider->setLink('https://github.com/doctrine-extensions/DoctrineExtensions/blob/main/doc/translatable.md#advanced-examples');
            $this->_em->persist($filmsProvider);
            $filmsProvider->mergeNewTranslations();
            $test = $filmsProvider->translate('ru')->getTitle();
            dump($test);die();
            $this->_em->flush();
        }
}
