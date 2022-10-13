<?php

namespace App\Tests\testByEndpoint;

use App\Entity\Provider;
use App\Tests\TestMain;


class ProviderTest extends TestMain
{

    public function testProviderCollection(): void
    {
        $this->getCollection('/api/providers', Provider::class);
    }



}