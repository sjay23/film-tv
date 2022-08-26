<?php

namespace App\Controller\Admin;

use App\Entity\FilmByProvider;
use App\Entity\People;
use App\Repository\PeopleRepository;
use App\Repository\FilmByProviderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PopularActorController extends AbstractController
{

    public function __construct(
        PeopleRepository $peopleRepository,
        FilmByProviderRepository $filmByProviderRepository,
    )
    {
        $this->peopleRepository = $peopleRepository;
        $this->filmByProviderRepository = $filmByProviderRepository;
    }

    public function getPopularActors(): array
    {
        return $this->peopleRepository->getPopularActor();
    }

    public function getFilmByActor($id): array
    {
        $people = $this->peopleRepository->findOneBy(['id'=>$id]);
        return $this->filmByProviderRepository->getFilmsByActor($people);
    }

}
