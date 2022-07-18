<?php

namespace App\Service;

use App\Entity\Country;
use App\Repository\CountryRepository;
use Doctrine\ORM\EntityManagerInterface;

class CountryService
{
    private EntityManagerInterface $entityManager;
    private CountryRepository $countryRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        CountryRepository $countryRepository

    ) {
        $this->entityManager = $entityManager;
        $this->countryRepository = $countryRepository;
    }

    public function getCountry($countryInput): ?Country
    {
        if (!$country = $this->countryRepository->findOneBy(['name' => $countryInput->getName()])) {
            $country = new Country();
            $country->setName($countryInput->getName());
            $this->entityManager->persist($country);
        }
        return $country;
    }
}
