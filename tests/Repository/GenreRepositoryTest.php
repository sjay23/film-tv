<?php

namespace App\Tests\Repository;

use App\Entity\Genre;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class GenreRepositoryTest extends KernelTestCase
{
    protected function setUp(): void
    {
        $this->containerKernel = static::getContainer();
        $this->genreRepository = $this->containerKernel->get(GenreRepository::class);
    }

    public function testAdd()
    {
        $genre =  new Genre();
        $genre->setName('Test_Genre');
        $this->genreRepository->add($genre,true);
        $this->genre = $this->genreRepository->findOneBy(['name'=>'Test_Genre']);

        $this->assertEquals('Test_Genre', $this->genre->getName());
    }

    public function testRemove()
    {
        $this->genre = $this->genreRepository->findOneBy([]);
        $id = $this->genre->getId();
        $this->genreRepository->remove($this->genre,true);

        $this->assertEquals(null , $this->genreRepository->findOneBy(['id'=>$id]));
    }
}
