<?php

namespace App\Tests;

use App\Service\Parsers\Megogo\FilmFieldService;

class ParseFilmTest extends TestUnitMain
{
    public function getService()
    {
        return new FilmFieldService($this->validator);
    }

    public function testParseFilm()
    {
        $age = $this->getService()->parseAge($this->getCrawlerByLink());
        $filmId = $this->getService()->parseFilmId($this->link);

        $this->assertEquals("7585835", $filmId);
        $this->assertEquals("18+", $age);
    }

}
