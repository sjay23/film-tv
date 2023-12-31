<?php

namespace App\Tests\Controller;

use App\Entity\People;
use App\Tests\TestMain;


class PeopleControllerTest extends TestMain
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
            'name' => 'test update'
        ]);
        $testUri = static::findIriBy(People::class, ['id' => $peopleId]);
        if ($testUri) {
            $this->sendGetUri($testUri);
        }
        $peopleRecordAfterUpdate = $this->peopleRepository->findOneBy(['id' => $peopleId]);
        $this->assertMatchesResourceItemJsonSchema(People::class);
        $this->assertEquals('test update', $peopleRecordAfterUpdate->getName());
        /**
         * Delete Comment
         */
        $id = $peopleRecordAfterUpdate->getId();
        $peopleDeleteUri = $this->router->generate('delete_people', array('id' => $id));
        $this->sendDeleteUri($peopleDeleteUri);
        $testUri = static::findIriBy(People::class, ['id' => $id]);
        $this->assertEquals(null, $testUri);
    }
}