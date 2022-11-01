<?php

namespace App\Tests;

use App\Service\Parsers\Megogo\FilmFieldService;

class ParseFilmTest extends TestUnitMain
{
    public function testParseFilm()
    {
        $filmFieldService = new FilmFieldService($this->validator);
        $contentHtml = $this->getContentLink($this->link);
        $crawler = $this->getCrawler($contentHtml);
        $age = $filmFieldService->parseAge($crawler);

        $filmId = $filmFieldService->parseFilmId($this->link);

        $this->assertEquals("7585835", $filmId);
        $this->assertEquals("18+", $age);
    }

}
