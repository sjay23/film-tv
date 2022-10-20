<?php

namespace App\Tests\testByEndpoint;

use App\Entity\Audio;
use App\Tests\TestMain;


class AudioTest extends TestMain
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->audioRepository = $this->entityManager
            ->getRepository(Audio::class);
        $this->idRecord = $this->audioRepository->findOneBy([])->getId();
    }

    public function testAudioCollection(): void
    {
        $this->getCollection('/api/audio', Audio::class);
    }

    public function testAudioRecord(): void
    {
        $this->getRecord(Audio::class);
    }

    public function testAddAudioRecord()
    {
        $audioUri = $this->router->generate('add_audio');

        /**
         * Insert Audio
         */
        $response = $this->sendPostUri($audioUri, [
            'name' => 'test title'
        ]);

        $responseRecord = json_decode($response->getContent());
        /**
         * @var Audio $audioRecord
         */
        $audioRecord = $this->audioRepository->findOneBy(['id' => $responseRecord->id]);

        $audioId = $audioRecord->getId();
        $testUri = static::findIriBy(Audio::class, ['id' => $audioId]);
        if ($testUri) {
            $this->sendGetUri($testUri);
        }

        $this->assertMatchesResourceItemJsonSchema(Audio::class);
        $this->assertEquals('test title', $audioRecord->getName());
        return $responseRecord->id;
    }

    public function testUpdateAudio(): void
    {
        $audioUri = $this->router->generate('update_audio',['id'=>$this->testAddAudioRecord()]);

        /**
         * Insert Audio
         */
        $response = $this->sendPostUriForUpdate($audioUri, [
            'name' => 'test title'
        ]);

        $responseRecord = json_decode($response->getContent());
        /**
         * @var Audio $audioRecord
         */
        $audioRecord = $this->audioRepository->findOneBy(['id' => $responseRecord->id]);

        $audioId = $audioRecord->getId();
        $testUri = static::findIriBy(Audio::class, ['id' => $audioId]);
        if ($testUri) {
            $this->sendGetUri($testUri);
        }

        $this->assertMatchesResourceItemJsonSchema(Audio::class);
        $this->assertEquals('test title', $audioRecord->getName());
    }

    public function testDeleteRecord(): void
    {
        $recordDeleteUri = $this->router->generate('delete_audio', array('id' => $this->idRecord));

        $this->sendDeleteUri($recordDeleteUri);

        $testUri = static::findIriBy(Audio::class, ['id' => $this->idRecord]);

        $this->assertEquals(null, $testUri);
    }
}
