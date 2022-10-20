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

    public function testAddGenreRecord()
    {
        $genreUri = $this->router->generate('add_genre');

        /**
         * Insert Genre
         */
        $response = $this->sendPostUri($genreUri, [
            'name' => 'test title'
        ]);

        $responseRecord = json_decode($response->getContent());
        /**
         * @var Genre $genreRecord
         */
        $genreRecord = $this->genreRepository->findOneBy(['id' => $responseRecord->id]);

        $genreId = $genreRecord->getId();
        $testUri = static::findIriBy(Genre::class, ['id' => $genreId]);
        if ($testUri) {
            $this->sendGetUri($testUri);
        }

        $this->assertMatchesResourceItemJsonSchema(Genre::class);
        $this->assertEquals('test title', $genreRecord->getName());
        return $responseRecord->id;
    }

    public function testUpdateGenre(): void
    {
        $genreUri = $this->router->generate('update_genre',['id'=>$this->testAddGenreRecord()]);

        /**
         * Insert Genre
         */
        $response = $this->sendPostUriForUpdate($genreUri, [
            'name' => 'test title'
        ]);

        $responseRecord = json_decode($response->getContent());
        /**
         * @var Genre $genreRecord
         */
        $genreRecord = $this->genreRepository->findOneBy(['id' => $responseRecord->id]);

        $genreId = $genreRecord->getId();
        $testUri = static::findIriBy(Genre::class, ['id' => $genreId]);
        if ($testUri) {
            $this->sendGetUri($testUri);
        }

        $this->assertMatchesResourceItemJsonSchema(Genre::class);
        $this->assertEquals('test title', $genreRecord->getName());
    }

    public function testDeleteRecord(): void
    {
        $recordDeleteUri = $this->router->generate('delete_genre', array('id' => $this->idRecord));

        $this->sendDeleteUri($recordDeleteUri);

        $testUri = static::findIriBy(Genre::class, ['id' => $this->idRecord]);

        $this->assertEquals(null, $testUri);
    }
}