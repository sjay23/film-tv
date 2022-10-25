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
        $filmUri = $this->router->generate('update_film',['id'=> 10]);
        $filesystem = new Filesystem();
        $filesystem->copy('./tests/image/test_image.png', './tests/image/test_image.png');
        $files = ['image' => [ new UploadedFile(
            './tests/image/test_image.png',
            'my_image.png',
            'image/png',
        )]];
        $response = $this->sendPostUriForUpdate($filmUri, [
            'image' => $files
        ]);
        dump($response->getContent());

        $responseRecord = json_decode($response->getContent());
        /**
         * @var FilmByProvider $filmRecord
         */
        $filmRecord = $this->filmRepository->findOneBy(['id' => $responseRecord->id]);

        $filmId = $filmRecord->getId();
        $testUri = static::findIriBy(FilmByProvider::class, ['id' => $filmId]);
        if ($testUri) {
            $this->sendGetUri($testUri);
        }

        $this->assertMatchesResourceItemJsonSchema(FilmByProvider::class);
        $this->assertEquals('16+', $filmRecord->getAge());
    }

    public function testDeleteRecord(): void
    {
        $recordDeleteUri = $this->router->generate('delete_filmByProvider', array('id' => $this->idRecord));

        $this->sendDeleteUri($recordDeleteUri);

        $testUri = static::findIriBy(FilmByProvider::class, ['id' => $this->idRecord]);

        $this->assertEquals(null, $testUri);
    }
}