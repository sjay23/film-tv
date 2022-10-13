<?php

namespace App\Tests\testByEndpoint;

use App\Entity\Country;
use App\Tests\TestMain;


class CountryTest extends TestMain
{
    public function testCountryCollection(): void
    {
        $this->getCollection('/api/countries', Country::class);
    }



}