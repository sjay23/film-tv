<?php

namespace App\Service;

use App\Entity\Image;
use Doctrine\ORM\EntityManagerInterface;

class ImageService
{
    private EntityManagerInterface $entityManager;


    public function __construct(
        EntityManagerInterface $entityManager

    ) {
        $this->entityManager = $entityManager;

    }

    public function getImage($imageInput)
    {
            $image = new Image();
            $image->setLink($imageInput->getLink());
            $this->entityManager->persist($image);
        $this->entityManager->flush();
            return $image;


    }
}