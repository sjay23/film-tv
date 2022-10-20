<?php

namespace App\Controller;

use App\DTO\AudioInput;
use App\DTO\ImageInput;
use App\Entity\Audio;
use App\Entity\FilmByProvider;
use App\Entity\Image;
use App\Repository\AudioRepository;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 *
 */
class ImageController
{
    /**
     * @var ValidatorInterface
     */
    private ValidatorInterface $validator;

    /**
     * @var ImageRepository
     */
    private ImageRepository $imageRepository;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @param ValidatorInterface $validator
     * @param ImageRepository $imageRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        ImageRepository $imageRepository,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
    ) {
        $this->imageRepository = $imageRepository;
        $this->validator = $validator;
        $this->entityManager = $entityManager;
    }

    /**
     * @param Request $request
     * @return Image
     * @throws Exception
     */
    public function addImage(Request $request): Image
    {
        $imageInput = new ImageInput(
            $request->get('link')
        );
        $this->validator->validate($imageInput);

        if ($image = $this->imageRepository->findOneBy(['link' => $imageInput->getLink()])) {
            throw new Exception('The image already exists');
        } else {
            $image = new Image();
            $image->setLink($imageInput->getLink());
            $this->entityManager->persist($image);
            $this->entityManager->flush();
        }
        return $image;
    }
    /**
     * @param Request $request
     * @return Audio
     * @throws Exception
     */
    public function updateImage(Request $request, Image $image): Image
    {
        $image->setLink($request->get('link'));
            $this->entityManager->flush();

        return $image;
    }

    public function deleteImage(Image $image): Image
    {
        $this->entityManager->remove($image);
        $this->entityManager->flush();

        return $image;
    }
}
