<?php

namespace App\Tests;

use App\Service\Parsers\Megogo\FilmFieldService;

class ParseFilmTest extends TestUnitMain
{
    public function getService()
    {
        return new FilmFieldService($this->validator);
    }

    public function testParseFilmAge()
    {
        $age = $this->getService()->parseAge($this->getCrawlerByLink());
        $this->assertEquals("18+", $age);
    }

    public function testParseFilmId()
    {
        $filmId = $this->getService()->parseFilmId($this->link);
        $this->assertEquals("7585835", $filmId);
    }

    public function testParseFilmDuration()
    {
        $duration = $this->getService()->parseDuration($this->getCrawlerByLink());
        $this->assertEquals("105", $duration);
    }

    public function testParseFilmYear()
    {
        $year = $this->getService()->parseAge($this->getCrawlerByLink());
        $this->assertEquals("18+", $year);
    }

    public function testParseFilmAudio()
    {
        $audio = $this->getService()->parseAudio($this->getCrawlerByLink());
        $this->assertEquals(" English ", ($audio[1])->getName());
    }

    public function testParseFilmGenre()
    {
        $genre = $this->getService()->parseGenre($this->getCrawlerByLink());
        $this->assertEquals("Thriller", ($genre[0])->getName());
    }

    public function testParseFilmCountry()
    {
        $country = $this->getService()->parseCountry($this->getCrawlerByLink());
        $this->assertEquals("USA", ($country[0])->getName());
    }

    public function testParseFilmRating()
    {
        $rating = $this->getService()->parseRating($this->getCrawlerByLink());
        $this->assertEquals("5.2", $rating);
    }

}
