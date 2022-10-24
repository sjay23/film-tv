<?php

namespace App\Tests\testByEndpoint;

use App\Entity\People;
use App\Tests\TestMain;


class PeopleTest extends TestMain
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->peopleRepository = $this->entityManager
            ->getRepository(People::class);
        $this->idRecord = $this->peopleRepository->findOneBy([])->getId();
    }

    public function testPeopleCollection(): void
    {
        $this->getCollection('/api/people/actors/popular', People::class);
    }

    public function testPeopleRecord(): void
    {
        $this->getRecord(People::class);
    }

    public function testPeople(): void
    {
        /**
         * Create
         */
        $peopleUri = $this->router->generate('add_people');
        $response = $this->sendPostUri($peopleUri, [
            'name' => 'test title',
            'link' => 'test link'
        ]);
        $responseRecord = json_decode($response->getContent());
        $peopleRecord = $this->peopleRepository->findOneBy(['id' => $responseRecord->id]);
        $peopleId = $peopleRecord->getId();
        $testUri = static::findIriBy(People::class, ['id' => $peopleId]);
        if ($testUri) {
            $this->sendGetUri($testUri);
        }
        $this->assertMatchesResourceItemJsonSchema(People::class);
        $this->assertEquals('test title', $peopleRecord->getName());
        /**
         * Update
         */
        $peopleUri = $this->router->generate('update_people', ['id' => $peopleId]);
        $this->sendPostUriForUpdate($peopleUri, [
            'name' => 'test title'
        ]);
        $testUri = static::findIriBy(People::class, ['id' => $peopleId]);
        if ($testUri) {
            $this->sendGetUri($testUri);
        }
        $this->assertMatchesResourceItemJsonSchema(People::class);
        $this->assertEquals('test title', $peopleRecord->getName());
        /**
         * Delete Comment
         */
        $peopleDeleteUri = $this->router->generate('delete_people', array('id' => $peopleId));
        $this->sendDeleteUri($peopleDeleteUri);
        $testUri = static::findIriBy(People::class, ['id' => $peopleId]);
        $this->assertEquals(null, $testUri);
    }
}