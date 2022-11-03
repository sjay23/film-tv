<?php

namespace App\Tests\testByService\SweetTvTests;

use App\Service\Parsers\SweetTv\FilmFieldService;
use App\Tests\testByService\TestUnitMain;

class ParseFilmTest extends TestUnitMain
{
    public const LINK = 'https://sweet.tv/en/movie/1347-rambo';

    public function getService()
    {
        return new FilmFieldService($this->validator);
    }

    public function testParseFilmAge()
    {
        $age = $this->getService()->parseAge($this->traitClass->getCrawlerByLink(self::LINK));
        $this->assertEquals("16+", $age);
    }

    public function testParseFilmId()
    {
        $filmId = $this->getService()->parseFilmId(self::LINK);
        $this->assertEquals("1347", $filmId);
    }

    public function testParseFilmDuration()
    {
        $duration = $this->getService()->parseDuration($this->traitClass->getCrawlerByLink(self::LINK));
        $this->assertEquals("5368", $duration);
    }

    public function testParseFilmYear()
    {
        $year = $this->getService()->parseAge($this->traitClass->getCrawlerByLink(self::LINK));
        $this->assertEquals("16+", $year);
    }

    public function testParseFilmAudio()
    {
        $audio = $this->getService()->parseAudio($this->traitClass->getCrawlerByLink(self::LINK));
        $this->assertEquals("Українська", ($audio[0])->getName());
    }

    public function testParseFilmGenre()
    {
        $genre = $this->getService()->parseGenre($this->traitClass->getCrawlerByLink(self::LINK));
        $this->assertEquals("Бойовики", ($genre[0])->getName());
    }

    public function testParseFilmCountry()
    {
        $country = $this->getService()->parseCountry($this->traitClass->getCrawlerByLink(self::LINK));
        $this->assertEquals("США", ($country[0])->getName());
    }

    public function testParseFilmRating()
    {
        $rating = $this->getService()->parseRating($this->traitClass->getCrawlerByLink(self::LINK));
        $this->assertEquals("7.0", $rating);
    }

}
