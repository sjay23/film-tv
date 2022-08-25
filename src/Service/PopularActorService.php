<?php

namespace App\Service;

use App\Repository\PeopleRepository;
use App\Repository\FilmByProviderRepository;

class PopularActorService
{
    private PeopleRepository $peopleRepository;
    private FilmByProviderRepository $filmByProviderRepository;

    public function __construct(
        FilmByProviderRepository $filmByProviderRepository,
        PeopleRepository $peopleRepository
    ) {
        $this->filmByProviderRepository = $filmByProviderRepository;
        $this->peopleRepository = $peopleRepository;
    }

    public function getPopularActor(): array
    {
        return $this->peopleRepository->getPopularActor();
    }

    public function getFilmByActor($actor): array
    {
        return $this->filmByProviderRepository->findBy(['actor' => $actor]);
    }
}
