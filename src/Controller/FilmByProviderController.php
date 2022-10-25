<?php

namespace App\Controller;

use App\DTO\AudioInput;
use App\Entity\Audio;
use App\Entity\FilmByProvider;
use App\Entity\Image;
use App\Repository\AudioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 *
 */
class FilmByProviderController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
    }

    public function updateFilm(Request $request , FilmByProvider $filmByProvider): FilmByProvider
    {
        $image = new Image();
        $image->setImageFile($request->files->get('poster'));
        $this->entityManager->persist($image);
        $filmByProvider->setPoster($image);
        $this->entityManager->flush();

        return $filmByProvider;
    }

    public function deleteFilmByProvider(FilmByProvider $filmByProvider): FilmByProvider
    {
        $this->entityManager->remove($filmByProvider);
        $this->entityManager->flush();

        return $filmByProvider;
    }
}
