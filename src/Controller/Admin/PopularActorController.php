<?php

namespace App\Controller\Admin;

use App\DTO\QueryParameter;
use App\Repository\PeopleRepository;
use App\Repository\FilmByProviderRepository;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;

class PopularActorController extends AbstractController
{
    private ValidatorInterface $validator;

    public function __construct(
        ValidatorInterface $validator,
        PeopleRepository $peopleRepository,
        FilmByProviderRepository $filmByProviderRepository,
        AdapterInterface $cache,
        PaginatorInterface $paginator
    )
    {
        $this->validator = $validator;
        $this->peopleRepository = $peopleRepository;
        $this->filmByProviderRepository = $filmByProviderRepository;
        $this->cache = $cache;
        $this->paginator = $paginator;
    }

    public function getPopularActors()
    {
        $cacheActors = $this->cache->getItem('actor_kay');
        if (!$cacheActors->isHit()) {
            $cacheActors->set($this->peopleRepository->getPopularActor());
            $cacheActors->expiresAfter(3600);
            $this->cache->save($cacheActors);
        }
        return $cacheActors->get();
    }

    public function getFilmByActor($id): array
    {
        $people = $this->peopleRepository->findOneBy(['id' => $id]);
        return $this->filmByProviderRepository->getFilmsByActor($people);
    }

    public function getFilmByParameter(Request $request)
    {
        $queryParameter = new QueryParameter();
        $queryParameter->setWord($request->get('word'));
        $queryParameter->setActorName($request->get('actor_name'));
        $queryParameter->setAudioLang($request->get('lang_audio'));
        $queryParameter->setDirectorName($request->get('director_name'));
        $queryParameter->setGenreName($request->get('genre_name'));
        $queryParameter->setRating($request->get('rating'));
        $queryParameter->setSortBy($request->get('sort_by'));
        $queryParameter->setYear($request->get('year'));
        $this->validator->validate($queryParameter);
        $appointments = $this->paginator->paginate(
            $this->filmByProviderRepository->getFilmsByFilters($queryParameter),
            $request->query->getInt('page', 1),
            5
        );

        return $appointments;
    }
}
