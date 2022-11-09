<?php

namespace App\Tests\Controller;

use App\Entity\Country;
use App\Tests\TestMain;


class CountryControllerTest extends TestMain
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

    public function testCountry(): void
    {
        /**
         * Create
         */
        $countryUri = $this->router->generate('add_country');
        $response = $this->sendPostUri($countryUri, [
            'name' => 'test title'
        ]);
        $responseRecord = json_decode($response->getContent());
        $countryRecord = $this->countryRepository->findOneBy(['id' => $responseRecord->id]);
        $countryId = $countryRecord->getId();
        $testUri = static::findIriBy(Country::class, ['id' => $countryId]);
        if ($testUri) {
            $this->sendGetUri($testUri);
        }
        $this->assertMatchesResourceItemJsonSchema(Country::class);
        $this->assertEquals('test title', $countryRecord->getName());
        /**
         * Update
         */
        $countryUpdateUri = $this->router->generate('update_country',['id'=>$countryId]);
        $response = $this->sendPostUriForUpdate($countryUpdateUri, [
            'name' => 'test title'
        ]);
        json_decode($response->getContent());
        $testUri = static::findIriBy(Country::class, ['id' => $countryId]);
        if ($testUri) {
            $this->sendGetUri($testUri);
        }
        $this->assertMatchesResourceItemJsonSchema(Country::class);
        $this->assertEquals('test title', $countryRecord->getName());
        /**
         * Delete Comment
         */
        $recordDeleteUri = $this->router->generate('delete_country', array('id' => $countryId));
        $this->sendDeleteUri($recordDeleteUri);
        $testUri = static::findIriBy(Country::class, ['id' => $countryId]);
        $this->assertEquals(null, $testUri);
    }
}