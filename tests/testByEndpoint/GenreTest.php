<?php

namespace App\Tests\testByEndpoint;

use App\Entity\Genre;
use App\Tests\TestMain;


class GenreTest extends TestMain
{
    public function testGenreCollection(): void
    {
        $this->getCollection('/api/genres', Genre::class);
    }

    public function testGenreRecord(): void
    {
        $this->getRecord(Genre::class);
    }

}