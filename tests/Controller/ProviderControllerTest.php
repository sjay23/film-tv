<?php

namespace App\Tests\Controller;

use App\Entity\Provider;
use App\Tests\TestMain;


class ProviderControllerTest extends TestMain
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->providerRepository = $this->entityManager
            ->getRepository(Provider::class);
        $this->idRecord = $this->providerRepository->findOneBy([])->getId();
    }

    public function testProviderCollection(): void
    {
        $this->getCollection('/api/providers', Provider::class);
    }

    public function testProviderRecord(): void
    {
        $this->getRecord(Provider::class);
    }

    public function testProvider(): void
    {
        /**
         * Create
         */
        $providerUri = $this->router->generate('add_provider');
        $response = $this->sendPostUri($providerUri, [
            'name' => 'test',
        ]);
        $responseRecord = json_decode($response->getContent());
        $providerRecord = $this->providerRepository->findOneBy(['id' => $responseRecord->id]);
        $providerId = $providerRecord->getId();
        $testUri = static::findIriBy(Provider::class, ['id' => $providerId]);
        if ($testUri) {
            $this->sendGetUri($testUri);
        }
        $this->assertMatchesResourceItemJsonSchema(Provider::class);
        $this->assertEquals('test', $providerRecord->getName());
        /**
         * Update
         */
        $providerUri = $this->router->generate('update_provider',['id'=> $providerId]);
        $this->sendPostUriForUpdate($providerUri, [
            'name' => 'test update'
        ]);
        $providerRecord = $this->providerRepository->findOneBy(['id' => $providerId]);
        $providerId = $providerRecord->getId();
        $testUri = static::findIriBy(Provider::class, ['id' => $providerId]);
        if ($testUri) {
            $this->sendGetUri($testUri);
        }
        $providerRecordAfterUpdate = $this->providerRepository->findOneBy(['id' => $providerId]);
        $this->assertMatchesResourceItemJsonSchema(Provider::class);
        $this->assertEquals('test update', $providerRecordAfterUpdate->getName());
        /**
         * Delete Comment
         */
        $id = $providerRecordAfterUpdate->getId();
        $recordDeleteUri = $this->router->generate('delete_provider', array('id' => $id));
        $this->sendDeleteUri($recordDeleteUri);
        $testUri = static::findIriBy(Provider::class, ['id' => $id]);
        $this->assertEquals(null, $testUri);
    }
}
