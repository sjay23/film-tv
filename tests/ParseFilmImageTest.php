<?php

namespace App\Tests;

use App\Service\Parsers\Megogo\FilmImageService;

class ParseFilmImageTest extends TestUnitMain
{
    public const LINK = 'https://megogo.net/en/view/7585835-crypto.html';

    public function getService()
    {
        return new FilmImageService($this->validator);
    }

    public function testParseFilmImage()
    {
        $images = $this->getService()->parseImage($this->getNodeFilms($this->getCrawlerByLink(self::LINK)));
        $this->assertEquals("link", ($images[0])->getLink());
    }
}
