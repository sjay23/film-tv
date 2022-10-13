<?php

namespace App\Tests\testByEndpoint;

use App\Entity\FilmByProviderTranslation;
use App\Tests\TestMain;


class FilmTranslationTest extends TestMain
{

    public function testFilmTranslationCollection(): void
    {
        $this->getCollection('/api/film_by_provider_translations', FilmByProviderTranslation::class);
    }



}