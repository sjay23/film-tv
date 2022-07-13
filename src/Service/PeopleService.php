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

    public function getPeople($peopleInput): People
    {
        if (!$people = $this->peopleRepository->findOneBy(['link' => $peopleInput->getLink()])) {
            $people = new People();
            $people->setName($peopleInput->getName());
            $people->setLink($peopleInput->getLink());
            $this->entityManager->persist($people);
        }
        return $people;
    }

    public function checkActors($peopleInput, array $actors): ?People
    {
        foreach ($actors as $actor) {
            /**
             * @var People $actor
             */
            if ($actor->getLink() === $peopleInput->getLink()) {
                return $actor;
            }
        }
        return null;
    }
}
