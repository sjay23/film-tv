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

    public function testAudio(): void
    {
        /**
         * Create
         */
        $audioUri = $this->router->generate('add_audio');
        $response = $this->sendPostUri($audioUri, [
            'name' => 'test title'
        ]);
        $responseRecord = json_decode($response->getContent());
        $audioRecord = $this->audioRepository->findOneBy(['id' => $responseRecord->id]);
        $audioId = $audioRecord->getId();
        $testUri = static::findIriBy(Audio::class, ['id' => $audioId]);
        if ($testUri) {
            $this->sendGetUri($testUri);
        }
        $this->assertMatchesResourceItemJsonSchema(Audio::class);
        $this->assertEquals('test title', $audioRecord->getName());
        /**
         * Update
         */
        $audioUpdateUri = $this->router->generate('update_audio',['id'=>$audioId]);
        $response = $this->sendPostUriForUpdate($audioUpdateUri, [
            'name' => 'test title'
        ]);
        json_decode($response->getContent());
        $testUri = static::findIriBy(Audio::class, ['id' => $audioId]);
        if ($testUri) {
            $this->sendGetUri($testUri);
        }
        $this->assertMatchesResourceItemJsonSchema(Audio::class);
        $this->assertEquals('test title', $audioRecord->getName());
        /**
         * Delete Comment
         */
        $recordDeleteUri = $this->router->generate('delete_audio', array('id' => $this->idRecord));
        $this->sendDeleteUri($recordDeleteUri);
        $testUri = static::findIriBy(Audio::class, ['id' => $this->idRecord]);
        $this->assertEquals(null, $testUri);
    }
}
