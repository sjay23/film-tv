<?php

namespace App\Controller\Admin;

use App\Entity\People;
use App\Repository\PeopleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PopularActorController extends AbstractController
{
     #/**
     #* @Route("/api/people/actors/popular", name="api_popular", methods={"GET"},defaults={"_api_resource_class" = People::class,})
     #*/
    #[Route(
        name: 'api_popular',
        path: '/api/people/actors/popular',
        methods: ['GET'],
        defaults: [
            '_api_resource_class' => People::class,
        ],
    )]
    public function getPopularActors( PeopleRepository $peopleRepository): People
    {
        return $peopleRepository->getPopularActor()[1];
    }
}
