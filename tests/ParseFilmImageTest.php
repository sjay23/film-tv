<?php

namespace App\Tests;

use App\Service\Parsers\Megogo\FilmImageService;

class ParseFilmImageTest extends TestUnitMain
{
    public function getService()
    {
        return new FilmImageService($this->validator);
    }

    public function testParseFilmImage()
    {
        $images = $this->getService()->parseImage($this->getNodeFilms($this->getCrawlerByLink()));
        $this->assertEquals("link", ($images[0])->getLink());
    }
}
