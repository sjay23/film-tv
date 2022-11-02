<?php

namespace App\Tests;

use App\Service\Parsers\Megogo\FilmPeopleService;

class ParseFilmPeopleTest extends TestUnitMain
{
    public function getService()
    {
        return new FilmPeopleService($this->validator);
    }

    public function testParseFilmDirector()
    {
        $cast = $this->getService()->parseDirector($this->getCrawlerByLink());
        $this->assertEquals("John Stalberg Jr.", ($cast[0])->getName());
    }
}
