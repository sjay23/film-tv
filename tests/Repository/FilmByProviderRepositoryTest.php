<?php

namespace App\Tests\Repository;

use App\DTO\QueryParameter;
use App\Entity\FilmByProvider;
use App\Repository\FilmByProviderRepository;
use App\Repository\PeopleRepository;
use App\Repository\ProviderRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class FilmByProviderRepositoryTest extends KernelTestCase
{
    protected function setUp(): void
    {
        $this->containerKernel = static::getContainer();
        $this->filmByProviderRepository = $this->containerKernel->get(FilmByProviderRepository::class);
        $this->peopleRepository = $this->containerKernel->get(PeopleRepository::class);
        $this->providerRepository = $this->containerKernel->get(ProviderRepository::class);
        $this->validator = $this->containerKernel->get(ValidatorInterface::class);
    }

    public function testGetFilmsByFilters()
    {
        $queryParameter = new QueryParameter();
        $queryParameter->setActorName('Chevy Chase');
        $queryParameter->setAudioLang('English');
        $queryParameter->setDirectorName('Michael Ritchie');
        $queryParameter->setGenreName('Comedy');
        $queryParameter->setWord('Fletch');
        $queryParameter->setYear('1989');
        $queryParameter->setRating('6.10');
        $queryParameter->setSortBy('id_asc');
        $this->validator->validate($queryParameter);
        $film = $this->filmByProviderRepository->getFilmsByFilters($queryParameter);

        $this->assertEquals('6.10', $film->getRating());
    }


    public function testGetFilmsByActor()
    {
        $film=$this->peopleRepository->findOneBy([]);
        $record = $this->filmByProviderRepository->getFilmsByActor($film->getId());

        $this->assertEquals( true, is_array($record));
    }

    public function testGetFilmByNoUploadedImage()
    {
        $provider=$this->providerRepository->findOneBy([]);
        $record = $this->filmByProviderRepository->getFilmByNoUploadedImage($provider->getId());

        $this->assertEquals( true, is_array($record));
    }

    public function testSave()
    {
        $film = new FilmByProvider();
        $film->setLink('test_link');
        $film->setMovieId('test_id');
        $this->filmByProviderRepository->save($film);
        $this->record = $this->filmByProviderRepository->findOneBy(['movieId'=>'test_id']);

        $this->assertEquals('test_link', $this->record->getLink());
    }

    public function testAdd()
    {
        $film = new FilmByProvider();
        $film->setLink('tests_link');
        $film->setMovieId('tests_id');
        $this->filmByProviderRepository->add($film,true);
        $this->record = $this->filmByProviderRepository->findOneBy(['movieId'=>'tests_id']);

        $this->assertEquals('tests_link', $this->record->getLink());
    }

    public function testDelete()
    {
        $this->film = $this->filmByProviderRepository->findOneBy([]);
        $id = $this->film->getId();
        $this->filmByProviderRepository->delete($this->film);

        $this->assertEquals(null , $this->filmByProviderRepository->findOneBy(['id'=>$id]));
    }

    public function testRemove()
    {
        $this->film = $this->filmByProviderRepository->findOneBy([]);
        $id = $this->film->getId();
        $this->filmByProviderRepository->remove($this->film,true);

        $this->assertEquals(null , $this->filmByProviderRepository->findOneBy(['id'=>$id]));
    }
}
