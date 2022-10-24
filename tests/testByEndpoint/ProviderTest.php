<?php

namespace App\Tests\testByEndpoint;

use App\Entity\Provider;
use App\Tests\TestMain;


class ProviderTest extends TestMain
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

    public function testUpdateProvider(): void
    {
        $providerUri = $this->router->generate('update_provider',['id'=>2]);

        /**
         * Insert Provider
         */
        $response = $this->sendPostUriForUpdate($providerUri, [
            'name' => 'test title'
        ]);

        $responseRecord = json_decode($response->getContent());
        /**
         * @var Provider $providerRecord
         */
        $providerRecord = $this->providerRepository->findOneBy(['id' => $responseRecord->id]);

        $providerId = $providerRecord->getId();
        $testUri = static::findIriBy(Provider::class, ['id' => $providerId]);
        if ($testUri) {
            $this->sendGetUri($testUri);
        }

        $this->assertMatchesResourceItemJsonSchema(Provider::class);
        $this->assertEquals('test title', $providerRecord->getName());
    }

    public function testDeleteRecord(): void
    {
        $recordDeleteUri = $this->router->generate('delete_provider', array('id' => $this->idRecord));

        $this->sendDeleteUri($recordDeleteUri);

        $testUri = static::findIriBy(Provider::class, ['id' => $this->idRecord]);

        $this->assertEquals(null, $testUri);
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
        dump($response->getContent());
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
        $providerUri = $this->router->generate('update_provider',['id'=>2]);
        $response = $this->sendPostUriForUpdate($providerUri, [
            'name' => 'test title'
        ]);
        $responseRecord = json_decode($response->getContent());

        $providerRecord = $this->providerRepository->findOneBy(['id' => $responseRecord->id]);
        $providerId = $providerRecord->getId();
        $testUri = static::findIriBy(Provider::class, ['id' => $providerId]);
        if ($testUri) {
            $this->sendGetUri($testUri);
        }
        $this->assertMatchesResourceItemJsonSchema(Provider::class);
        $this->assertEquals('test title', $providerRecord->getName());
        /**
         * Delete Comment
         */
        $recordDeleteUri = $this->router->generate('delete_provider', array('id' => $this->idRecord));
        $this->sendDeleteUri($recordDeleteUri);
        $testUri = static::findIriBy(Provider::class, ['id' => $this->idRecord]);
        $this->assertEquals(null, $testUri);
    }
}