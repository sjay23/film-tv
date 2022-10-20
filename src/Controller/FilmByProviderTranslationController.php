<?php

namespace App\Controller;

use App\DTO\AudioInput;
use App\Entity\Audio;
use App\Entity\FilmByProvider;
use App\Entity\FilmByProviderTranslation;
use App\Repository\AudioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 *
 */
class FilmByProviderTranslationController
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

    /**
     * @param Request $request
     * @return FilmByProviderTranslation
     * @throws Exception
     */
    public function updateFilmTranslation(Request $request, FilmByProvider $filmByProvider): FilmByProviderTranslation
    {
        $filmByProviderTranslation = $filmByProvider->translate('en');
        $filmByProviderTranslation->setTitle($request->get('title'));
            $this->entityManager->flush();

        return $filmByProviderTranslation;
    }

    public function deleteFilmByProviderTranslation(FilmByProviderTranslation $filmByProviderTranslation): FilmByProviderTranslation
    {
        $this->entityManager->remove($filmByProviderTranslation);
        $this->entityManager->flush();

        return $filmByProviderTranslation;
    }
}
