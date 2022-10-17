<?php

namespace App\Tests\testByEndpoint;

use App\Entity\Audio;
use App\Tests\TestMain;


class AudioTest extends TestMain
{
    public function testAudioCollection(): void
    {
        $this->getCollection('/api/audio', Audio::class);
    }

    public function testAudioRecord(): void
    {
        $this->getRecord(Audio::class);
    }

}