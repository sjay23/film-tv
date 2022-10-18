<?php

namespace App\Tests\testByEndpoint;

use App\Entity\FilmByProvider;
use App\Tests\TestMain;


class FilmTest extends TestMain
{
    public function testFilmByProviderCollection(): void
    {
        $this->getCollection('/api/film/', FilmByProvider::class);
    }

    public function testFilmRecord(): void
    {
        $this->getRecord(FilmByProvider::class);
    }

}