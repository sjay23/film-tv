<?php

namespace App\Controller;

use App\DTO\GenreInput;
use App\Entity\Genre;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GenreController
{
    /**
     * @var ValidatorInterface
     */
    private ValidatorInterface $validator;

    /**
     * @var GenreRepository
     */
    private GenreRepository $genreRepository;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @param ValidatorInterface $validator
     * @param GenreRepository $genreRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        GenreRepository $genreRepository,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
    ) {
        $this->genreRepository = $genreRepository;
        $this->validator = $validator;
        $this->entityManager = $entityManager;
    }

    /**
     * @param Request $request
     * @return Genre
     * @throws Exception
     */
    public function addGenre(Request $request): Genre
    {
        $genreInput = new GenreInput(
            $request->get('name')
        );
        $this->validator->validate($genreInput);

        if ($genre = $this->genreRepository->findOneBy(['name' => $genreInput->getName()])) {
            throw new Exception('The genre already exists');
        } else {
            $genre = new Genre();
            $genre->setName($genreInput->getName());
            $this->entityManager->persist($genre);
            $this->entityManager->flush();
        }
        return $genre;
    }

    /**
     * @param Request $request
     * @return Genre
     * @throws Exception
     */
    public function updateGenre(Request $request, Genre $genre): Genre
    {
        $genre->setName($request->get('name'));
        $this->entityManager->flush();

        return $genre;
    }

    public function deleteGenre(Genre $genre): Genre
    {
        $this->entityManager->remove($genre);
        $this->entityManager->flush();

        return $genre;
    }
}
