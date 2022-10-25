<?php

namespace App\Tests\testByEndpoint;

use App\Entity\FilmByProvider;
use App\Tests\TestMain;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class FilmTest extends TestMain
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->filmRepository = $this->entityManager
            ->getRepository(FilmByProvider::class);
        $this->idRecord = $this->filmRepository->findOneBy([])->getId();
    }

    public function testFilmByProviderCollection(): void
    {
        $this->getCollection('/api/film/', FilmByProvider::class);
    }

    public function testFilmRecord(): void
    {
        $this->getRecord(FilmByProvider::class);
    }

    public function testUpdateFilmByProvider(): void
    {
        $filmUri = $this->router->generate('update_film',['id'=> 11]);
        $files =  new UploadedFile(
            './tests/image/test_image1.png',
            'my_image.png',
            'image/png',
        );
        $this->sendPostUriForUploadFile($filmUri, [
            'poster' => $files
        ]);
    }

    public function testDeleteRecord(): void
    {
        $recordDeleteUri = $this->router->generate('delete_filmByProvider', array('id' => $this->idRecord));

        $this->sendDeleteUri($recordDeleteUri);

        $testUri = static::findIriBy(FilmByProvider::class, ['id' => $this->idRecord]);

        $this->assertEquals(null, $testUri);
    }
}