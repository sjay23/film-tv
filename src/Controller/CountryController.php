<?php

namespace App\Controller;

use App\DTO\CountryInput;
use App\Entity\Country;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CountryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CountryController
{
    /**
     * @var ValidatorInterface
     */
    private ValidatorInterface $validator;

    /**
     * @var CountryRepository
     */
    private CountryRepository $countryRepository;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @param ValidatorInterface $validator
     * @param CountryRepository $countryRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        CountryRepository $countryRepository,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
    )
    {
        $this->countryRepository = $countryRepository;
        $this->validator = $validator;
        $this->entityManager = $entityManager;
    }

    function addCountry(Request $request): Country
    {

        $countryInput = new CountryInput(
            $request->get('name')
        );
        $this->validator->validate($countryInput);

        if ($country = $this->countryRepository->findOneBy(['name' => $countryInput->getName()])) {
            throw new \Exception('The country already exists');
        } else {
            $country = new Country();
            $country->setName($countryInput->getName());
            $this->entityManager->persist($country);
            $this->entityManager->flush();
        }
        return $country;
    }
}