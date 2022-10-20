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

    public function testAddPeopleRecord()
    {
        $peopleUri = $this->router->generate('add_people');

        /**
         * Insert People
         */
        $response = $this->sendPostUri($peopleUri, [
            'name' => 'test title',
            'link' => 'test link'
        ]);

        $responseRecord = json_decode($response->getContent());
        /**
         * @var People $peopleRecord
         */
        $peopleRecord = $this->peopleRepository->findOneBy(['id' => $responseRecord->id]);

        $peopleId = $peopleRecord->getId();
        $testUri = static::findIriBy(People::class, ['id' => $peopleId]);
        if ($testUri) {
            $this->sendGetUri($testUri);
        }

        $this->assertMatchesResourceItemJsonSchema(People::class);
        $this->assertEquals('test title', $peopleRecord->getName());
        return $responseRecord;
    }

    public function testUpdatePeople()
    {
        $peopleUri = $this->router->generate('update_people',['id'=>$this->testAddPeopleRecord()->id]);

        /**
         * Insert People
         */
        $response = $this->sendPostUriForUpdate($peopleUri, [
            'name' => 'test title'
        ]);

        $responseRecord = json_decode($response->getContent());
        /**
         * @var People $peopleRecord
         */
        $peopleRecord = $this->peopleRepository->findOneBy(['id' => $responseRecord->id]);

        $peopleId = $peopleRecord->getId();
        $testUri = static::findIriBy(People::class, ['id' => $peopleId]);
        if ($testUri) {
            $this->sendGetUri($testUri);
        }

        $this->assertMatchesResourceItemJsonSchema(People::class);
        $this->assertEquals('test title', $peopleRecord->getName());
        return $responseRecord->id;
    }

    public function testDeleteImage(): void
    {
        $peopleDeleteUri = $this->router->generate('delete_people', array('id' => $this->idRecord));

        $this->sendDeleteUri($peopleDeleteUri);

        $testUri = static::findIriBy(People::class, ['id' => $this->idRecord]);

        $this->assertEquals(null, $testUri);
    }
}