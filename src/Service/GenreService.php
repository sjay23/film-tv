<?php

namespace App\Service;

use App\Entity\Genre;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManagerInterface;

class GenreService
{
    private EntityManagerInterface $entityManager;
    private GenreRepository $genreRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        GenreRepository $genreRepository

    ) {
        $this->entityManager = $entityManager;
        $this->genreRepository = $genreRepository;

    }

    public function getGenre($genreInput)
    {
        if ($this->genreRepository->findOneBy(['name' => $genreInput->getName()])) {
            $genre = $this->genreRepository->findOneBy(['name' => $genreInput->getName()]);
            return $genre;
        } else {
            $genre = new Genre();
            $genre->setName($genreInput->getName());
            $this->entityManager->persist($genre);
            return $genre;
        }

    }
}