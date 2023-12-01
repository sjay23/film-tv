<?php

namespace App\Tests\testByService\SweetTvTests;

use App\Service\Parsers\SweetTv\FilmFieldService;
use App\Tests\testByService\TestUnitMain;
use ReflectionException;

class ParseFilmTest extends TestUnitMain
{
    public const LINK_EN = 'https://sweet.tv/en/movie/1347-rambo';
    public const LINK_RU = 'https://sweet.tv/ru/movie/1347-rambo';
    public const LINK_UK = 'https://sweet.tv/uk/movie/1347-rambo';

    public function getService()
    {
        return new FilmFieldService($this->validator);
    }

    public function testParseFilmAge()
    {
        $age = $this->getService()->parseAge($this->traitClass->getCrawlerByLink(
            self::LINK_EN,
            'en',
            'en',
            $this->getCookies($this->sweetTvService, 'en')
        ));
        $this->assertEquals("16+", $age);
    }

    public function testParseFilmId()
    {
        $filmId = $this->getService()->parseFilmId(self::LINK_EN);
        $this->assertEquals("1347", $filmId);
    }

    public function testParseFilmDuration()
    {
        $duration = $this->getService()->parseDuration($this->traitClass->getCrawlerByLink(
            self::LINK_EN,
            'en',
            'en',
            $this->getCookies($this->sweetTvService, 'en')
        ));
        $this->assertEquals("5368", $duration);
    }

    public function testParseFilmYear()
    {
        $year = $this->getService()->parseYear($this->traitClass->getCrawlerByLink(
            self::LINK_EN,
            'en',
            'en',
            $this->getCookies($this->sweetTvService, 'en')
        ));
        $this->assertEquals("2007", $year);
    }

    public function testParseFilmAudio()
    {
        $audio = $this->getService()->parseAudio($this->traitClass->getCrawlerByLink(
            self::LINK_EN,
            'en',
            'en',
            $this->getCookies($this->sweetTvService, 'en')
        ));
        $this->assertEquals("Ukrainian", ($audio[0])->getName());

        $audio = $this->getService()->parseAudio($this->traitClass->getCrawlerByLink(
            self::LINK_RU,
            'ru',
            'ru',
            $this->getCookies($this->sweetTvService, 'ru')
        ));
        $this->assertEquals("Русский", ($audio[0])->getName());

        $audio = $this->getService()->parseAudio($this->traitClass->getCrawlerByLink(
            self::LINK_UK,
            'uk',
            'uk',
            $this->getCookies($this->sweetTvService, 'uk')
        ));
        $this->assertEquals("Українська", ($audio[0])->getName());
    }

    public function testParseFilmGenre()
    {
        $genre = $this->getService()->parseGenre($this->traitClass->getCrawlerByLink(
            self::LINK_EN,
            'en',
            'en',
            $this->getCookies($this->sweetTvService, 'en')
        ));
        $this->assertEquals("Action", ($genre[0])->getName());

        $genre = $this->getService()->parseGenre($this->traitClass->getCrawlerByLink(
            self::LINK_RU,
            'ru',
            'ru',
            $this->getCookies($this->sweetTvService, 'ru')
        ));
        $this->assertEquals("Боевики", ($genre[0])->getName());

        $genre = $this->getService()->parseGenre($this->traitClass->getCrawlerByLink(
            self::LINK_UK,
            'uk',
            'uk',
            $this->getCookies($this->sweetTvService, 'uk')
        ));
        $this->assertEquals("Бойовики", ($genre[0])->getName());
    }

    public function testParseFilmCountry()
    {
        $country = $this->getService()->parseCountry($this->traitClass->getCrawlerByLink(
            self::LINK_EN,
            'en',
            'en',
            $this->getCookies($this->sweetTvService, 'en')
        ));
        $this->assertEquals("USA", ($country[0])->getName());

        $country = $this->getService()->parseCountry($this->traitClass->getCrawlerByLink(
            self::LINK_RU,
            'ru',
            'ru',
            $this->getCookies($this->sweetTvService, 'ru')
        ));
        $this->assertEquals("США", ($country[0])->getName());

        $country = $this->getService()->parseCountry($this->traitClass->getCrawlerByLink(
            self::LINK_UK,
            'uk',
            'uk',
            $this->getCookies($this->sweetTvService, 'uk')
        ));
        $this->assertEquals("США", ($country[0])->getName());
    }

    public function testParseFilmRating()
    {
        $rating = $this->getService()->parseRating($this->traitClass->getCrawlerByLink(
            self::LINK_EN,
            'en',
            'en',
            $this->getCookies($this->sweetTvService, 'en')
        ));
        $this->assertEquals("7.0", $rating);
    }

}
