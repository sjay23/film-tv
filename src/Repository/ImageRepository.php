<?php

namespace App\Repository;

use App\Entity\Image;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Carbon\Carbon;

/**
 * @extends ServiceEntityRepository<Image>
 *
 * @method Image|null find($id, $lockMode = null, $lockVersion = null)
 * @method Image|null findOneBy(array $criteria, array $orderBy = null)
 * @method Image[]    findAll()
 * @method Image[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Image::class);
    }

    public function add(Image $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Image $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function setImageLink(Image $image, string $link): Image
    {
        $image->setLink($link);
        $image->setUploadedAt(Carbon::now());
        $image->setUploaded($image::NO_UPLOAD);
        return $image;
    }

    /**
     * @param Image $provider
     */
    public function save(Image $provider)
    {
        $this->_em->persist($provider);
        $this->_em->flush();
    }

    /**
     * @param Image $provider
     */
    public function delete(Image $provider)
    {
        $this->_em->remove($provider);
        $this->_em->flush();
    }


    public function getNoUploadedImage()
    {
        return $this->createQueryBuilder('i')
            ->select('i')
            ->where('i.uploaded == 0')
            ->getQuery()
            ->getResult();
    }
}
