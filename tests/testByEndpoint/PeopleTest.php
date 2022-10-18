<?php

namespace App\Tests\testByEndpoint;

use App\Entity\People;
use App\Tests\TestMain;


class PeopleTest extends TestMain
{
    public function testPeopleCollection(): void
    {
        $this->getCollection('/api/people/actors/popular', People::class);
    }

    public function testPeopleRecord(): void
    {
        $this->getRecord(People::class);
    }

}