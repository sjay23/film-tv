<?php

namespace App\Tests\testByEndpoint;

use App\Entity\Country;
use App\Tests\TestMain;


class CountryTest extends TestMain
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->countryRepository = $this->entityManager
            ->getRepository(Country::class);
        $this->idRecord = $this->countryRepository->findOneBy([])->getId();
    }

    public function testCountryCollection(): void
    {
        $this->getCollection('/api/countries', Country::class);
    }

    public function testCountryRecord(): void
    {
        $this->getRecord(Country::class);
    }

    public function testAddCountryRecord()
    {
        $countryUri = $this->router->generate('add_country');

        /**
         * Insert Country
         */
        $response = $this->sendPostUri($countryUri, [
            'name' => 'test title'
        ]);

        $responseRecord = json_decode($response->getContent());
        /**
         * @var Country $countryRecord
         */
        $countryRecord = $this->countryRepository->findOneBy(['id' => $responseRecord->id]);

        $countryId = $countryRecord->getId();
        $testUri = static::findIriBy(Country::class, ['id' => $countryId]);
        if ($testUri) {
            $this->sendGetUri($testUri);
        }

        $this->assertMatchesResourceItemJsonSchema(Country::class);
        $this->assertEquals('test title', $countryRecord->getName());
        return $responseRecord->id;
    }

    public function testUpdateCountry(): void
    {
        $countryUri = $this->router->generate('update_country',['id'=>$this->testAddCountryRecord()]);

        /**
         * Insert Country
         */
        $response = $this->sendPostUriForUpdate($countryUri, [
            'name' => 'test title'
        ]);

        $responseRecord = json_decode($response->getContent());
        /**
         * @var Country $countryRecord
         */
        $countryRecord = $this->countryRepository->findOneBy(['id' => $responseRecord->id]);

        $countryId = $countryRecord->getId();
        $testUri = static::findIriBy(Country::class, ['id' => $countryId]);
        if ($testUri) {
            $this->sendGetUri($testUri);
        }

        $this->assertMatchesResourceItemJsonSchema(Country::class);
        $this->assertEquals('test title', $countryRecord->getName());
    }

    public function testDeleteRecord(): void
    {
        $recordDeleteUri = $this->router->generate('delete_country', array('id' => $this->idRecord));

        $this->sendDeleteUri($recordDeleteUri);

        $testUri = static::findIriBy(Country::class, ['id' => $this->idRecord]);

        $this->assertEquals(null, $testUri);
    }
}