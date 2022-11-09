<?php

namespace App\Tests\Controller;

use App\Entity\FilmByProviderTranslation;
use App\Tests\TestMain;


class FilmByProviderTranslationControllerTest extends TestMain
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->filmRepository = $this->entityManager
            ->getRepository(FilmByProviderTranslation::class);
        $this->idRecord = $this->filmRepository->findOneBy([])->getId();
    }

    public function testFilmTranslationCollection(): void
    {
        $this->getCollection('/api/film_by_provider_translations', FilmByProviderTranslation::class);
    }

    public function testFilmByProviderTranslationRecord(): void
    {
        $this->getRecord(FilmByProviderTranslation::class);
    }

    public function testUpdateFilmByProviderTranslation(): void
    {
        $filmUri = $this->router->generate('update_filmTranslation',['id'=> $this->idRecord]);

        /**
         * Insert FilmByProviderTranslation
         */
        $response = $this->sendPostUriForUpdate($filmUri, [
            'title' => 'test title'
        ]);

        $responseRecord = json_decode($response->getContent());
        /**
         * @var FilmByProviderTranslation $filmRecord
         */
        $filmRecord = $this->filmRepository->findOneBy(['id' => $responseRecord->id]);

        $filmId = $filmRecord->getId();
        $testUri = static::findIriBy(FilmByProviderTranslation::class, ['id' => $filmId]);
        if ($testUri) {
            $this->sendGetUri($testUri);
        }

        $this->assertMatchesResourceItemJsonSchema(FilmByProviderTranslation::class);
        $this->assertEquals('test title', $filmRecord->getTitle());
    }

    public function testDeleteRecord(): void
    {
        $recordDeleteUri = $this->router->generate('delete_filmByProviderTranslation', array('id' => $this->idRecord));

        $this->sendDeleteUri($recordDeleteUri);

        $testUri = static::findIriBy(FilmByProviderTranslation::class, ['id' => $this->idRecord]);

        $this->assertEquals(null, $testUri);
    }
}