<?php

namespace App\Tests\testByEndpoint;

use App\Entity\FilmByProvider;
use App\Tests\TestMain;


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
        $filmUri = $this->router->generate('update_film',['id'=> $this->idRecord]);

        /**
         * Insert FilmByProvider
         */
        $response = $this->sendPostUriForUpdate($filmUri, [
            'age' => '16+'
        ]);

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