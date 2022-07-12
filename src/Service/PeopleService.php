<?php

namespace App\Service;

use App\Entity\People;
use App\Repository\PeopleRepository;
use Doctrine\ORM\EntityManagerInterface;

class PeopleService
{
    private EntityManagerInterface $entityManager;
    private PeopleRepository $peopleRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        PeopleRepository $peopleRepository

    ) {
        $this->entityManager = $entityManager;
        $this->peopleRepository = $peopleRepository;

    }

    public function getPeople($peopleInput)
    {
        $people= $this->peopleRepository->findOneBy(['link' => $peopleInput->getLink()]);
        $peopleName= $this->peopleRepository->findOneBy(['name' => $peopleInput->getName()]);
        $peopleLink= $this->peopleRepository->findOneBy(['link' => $peopleInput->getLink()]);
        if ($peopleName == null and $peopleLink == null) {
            $people = new People();
            $people->setName($peopleInput->getName());
            $people->setLink($peopleInput->getLink());
            $this->entityManager->persist($people);
        }
        return $people;
    }
}
