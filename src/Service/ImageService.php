<?php

namespace App\Service;

use App\Entity\Image;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;

class ImageService
{
    private EntityManagerInterface $entityManager;
    private ImageRepository $imageRepository;


    public function __construct(
        EntityManagerInterface $entityManager,
        ImageRepository $imageRepository
    ) {
        $this->entityManager = $entityManager;
        $this->imageRepository = $imageRepository;
    }

    public function getImage($imageInput): Image
    {
        $link = $imageInput->getLink();
        if (!$image = $this->imageRepository->findOneBy(['link' => $link])) {
            $image = new Image($link);
            $this->entityManager->persist($image);
            $this->entityManager->flush();
        }
        return $image;
    }
}
