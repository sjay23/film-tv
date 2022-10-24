<?php

namespace App\Tests\testByEndpoint;

use App\Entity\Genre;
use App\Tests\TestMain;


class GenreTest extends TestMain
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->genreRepository = $this->entityManager
            ->getRepository(Genre::class);
        $this->idRecord = $this->genreRepository->findOneBy([])->getId();
    }

    public function testGenreCollection(): void
    {
        $this->getCollection('/api/genres', Genre::class);
    }

    public function testGenreRecord(): void
    {
        $this->getRecord(Genre::class);
    }

    public function testGenre(): void
    {
        /**
         * Create
         */
        $genreUri = $this->router->generate('add_genre');
        $response = $this->sendPostUri($genreUri, [
            'name' => 'test title'
        ]);
        $responseRecord = json_decode($response->getContent());
        $genreRecord = $this->genreRepository->findOneBy(['id' => $responseRecord->id]);
        $genreId = $genreRecord->getId();
        $testUri = static::findIriBy(Genre::class, ['id' => $genreId]);
        if ($testUri) {
            $this->sendGetUri($testUri);
        }
        $this->assertMatchesResourceItemJsonSchema(Genre::class);
        $this->assertEquals('test title', $genreRecord->getName());
        /**
         * Update
         */
        $genreUpdateUri = $this->router->generate('update_genre', ['id' => $genreId]);
        $this->sendPostUriForUpdate($genreUpdateUri, [
            'name' => 'test title'
        ]);
        $testUri = static::findIriBy(Genre::class, ['id' => $genreId]);
        if ($testUri) {
            $this->sendGetUri($testUri);
        }
        $this->assertMatchesResourceItemJsonSchema(Genre::class);
        $this->assertEquals('test title', $genreRecord->getName());
        /**
         * Delete Comment
         */
        $recordDeleteUri = $this->router->generate('delete_genre', array('id' => $genreId));
        $this->sendDeleteUri($recordDeleteUri);
        $testUri = static::findIriBy(Genre::class, ['id' => $genreId]);
        $this->assertEquals(null, $testUri);
    }
}