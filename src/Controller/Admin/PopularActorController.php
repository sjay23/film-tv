<?php

namespace App\Controller\Admin;

use App\Entity\People;
use App\Repository\PeopleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PopularActorController extends AbstractController
{
    public function getPopularActors( PeopleRepository $peopleRepository): array
    {
        return $peopleRepository->getPopularActor();
    }
}
