<?php

namespace App\Tests\testByService\MegogoTests;

use App\Service\Parsers\Megogo\FilmFieldService;
use App\Tests\testByService\TestUnitMain;

class ParseFilmTest extends TestUnitMain
{
    public const LINK_EN = 'https://megogo.net/en/view/7585835-crypto.html';
    public const LINK_RU = 'https://megogo.net/ru/view/7585835-crypto.html';
    public const LINK_UK = 'https://megogo.net/uk/view/7585835-crypto.html';

    public function getService()
    {
        return new FilmFieldService($this->validator);
    }

    public function testParseFilmAge()
    {
        $age = $this->getService()->parseAge($this->traitClass->getCrawlerByLink(self::LINK_EN));
        $this->assertEquals("18+", $age);
    }

    public function testParseFilmId()
    {
        $filmId = $this->getService()->parseFilmId(self::LINK_EN);
        $this->assertEquals("7585835", $filmId);
    }

    public function testParseFilmDuration()
    {
        $duration = $this->getService()->parseDuration($this->traitClass->getCrawlerByLink(self::LINK_EN));
        $this->assertEquals("105", $duration);
    }

    public function testParseFilmYear()
    {
        $year = $this->getService()->parseAge($this->traitClass->getCrawlerByLink(self::LINK_EN));
        $this->assertEquals("18+", $year);
    }

    public function testParseFilmAudio()
    {
        $audio_en = $this->getService()->parseAudio($this->traitClass->getCrawlerByLink(self::LINK_EN));
        $audio_ru = $this->getService()->parseAudio($this->traitClass->getCrawlerByLink(self::LINK_RU));
        $audio_uk = $this->getService()->parseAudio($this->traitClass->getCrawlerByLink(self::LINK_UK));
        $this->assertEquals(" English ", ($audio_en[1])->getName());
        $this->assertEquals("Русский", ($audio_ru[0])->getName());
        $this->assertEquals(" Англійська", ($audio_uk[1])->getName());
    }

    public function testParseFilmGenre()
    {
        $genre_en = $this->getService()->parseGenre($this->traitClass->getCrawlerByLink(self::LINK_EN));
        $genre_ru = $this->getService()->parseGenre($this->traitClass->getCrawlerByLink(self::LINK_RU));
        $genre_uk = $this->getService()->parseGenre($this->traitClass->getCrawlerByLink(self::LINK_UK));
        $this->assertEquals("Thriller", ($genre_en[0])->getName());
        $this->assertEquals("Триллеры", ($genre_ru[0])->getName());
        $this->assertEquals("Трилери", ($genre_uk[0])->getName());
    }

    public function testParseFilmCountry()
    {
        $country_en = $this->getService()->parseCountry($this->traitClass->getCrawlerByLink(self::LINK_EN));
        $country_ru = $this->getService()->parseCountry($this->traitClass->getCrawlerByLink(self::LINK_RU));
        $country_uk = $this->getService()->parseCountry($this->traitClass->getCrawlerByLink(self::LINK_UK));
        $this->assertEquals("USA", ($country_en[0])->getName());
        $this->assertEquals("США", ($country_ru[0])->getName());
        $this->assertEquals("США", ($country_uk[0])->getName());
    }

    public function testParseFilmRating()
    {
        $rating = $this->getService()->parseRating($this->traitClass->getCrawlerByLink(self::LINK_EN));
        $this->assertEquals("5.2", $rating);
    }

}
