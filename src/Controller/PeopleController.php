<?php

namespace App\Controller;

use App\DTO\AudioInput;
use App\DTO\PeopleInput;
use App\Entity\Audio;
use App\Entity\FilmByProvider;
use App\Entity\People;
use App\Repository\AudioRepository;
use App\Repository\PeopleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 *
 */
class PeopleController
{
    /**
     * @var ValidatorInterface
     */
    private ValidatorInterface $validator;

    /**
     * @var PeopleRepository
     */
    private PeopleRepository $peopleRepository;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @param ValidatorInterface $validator
     * @param PeopleRepository $peopleRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        PeopleRepository $peopleRepository,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
    ) {
        $this->peopleRepository = $peopleRepository;
        $this->validator = $validator;
        $this->entityManager = $entityManager;
    }

    /**
     * @param Request $request
     * @return People
     * @throws Exception
     */
    public function addPeople(Request $request): People
    {
        $peopleInput = new PeopleInput(
            $request->get('name'), $request->get('link')
        );
        $this->validator->validate($peopleInput);

        if ($people = $this->peopleRepository->findOneBy(['name' => $peopleInput->getName()])) {
            throw new Exception('The people already exists');
        } else {
            $people = new People();
            $people->setName($peopleInput->getName());
            $this->entityManager->persist($people);
            $this->entityManager->flush();
        }
        return $people;
    }

    /**
     * @param Request $request
     * @return Audio
     * @throws Exception
     */
    public function updatePeople(Request $request, People $people): People
    {
        $people->setName($request->get('name'));
            $this->entityManager->flush();

        return $people;
    }

    public function deletePeople( People $people): People
    {
        $this->entityManager->remove($people);
        $this->entityManager->flush();

        return $people;
    }
}
