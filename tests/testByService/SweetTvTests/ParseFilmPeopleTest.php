<?php

namespace App\Tests\testByService\SweetTvTests;

use App\Service\Parsers\MainParserService;
use App\Service\Parsers\SweetTv\FilmPeopleService;
use App\Tests\testByService\TestUnitMain;
use GuzzleHttp\Exception\GuzzleException;
use ReflectionException;

class ParseFilmPeopleTest extends TestUnitMain
{
    public const LINK_RU = 'https://sweet.tv/ru/movie/1347-rambo';
    public const LINK_EN = 'https://sweet.tv/en/movie/1347-rambo';
    public const LINK_UK = 'https://sweet.tv/uk/movie/1347-rambo';

    public function getService()
    {
        return new FilmPeopleService($this->validator);
    }

    public function testParseFilmDirector()
    {
        $cast = $this->getService()->parseDirector($this->traitClass->getCrawlerByLink(
            self::LINK_RU,
            'ru',
            'ru',
            $this->getCookies($this->sweetTvService, 'ru')
        ));
        $this->assertEquals("Сильвестр Сталлоне", ($cast[0])->getName());

        $cast = $this->getService()->parseDirector($this->traitClass->getCrawlerByLink(
            self::LINK_EN,
            'en',
            'en',
            $this->getCookies($this->sweetTvService, 'en')
        ));
        $this->assertEquals("Sylvester Stallone", ($cast[0])->getName());

        $cast = $this->getService()->parseDirector($this->traitClass->getCrawlerByLink(
            self::LINK_UK,
            'uk',
            'uk',
            $this->getCookies($this->sweetTvService, 'uk')
        ));
        $this->assertEquals("Сильвестр Сталлоне", ($cast[0])->getName());
    }

    /**
     * @throws ReflectionException
     * @throws GuzzleException
     */
    public function testParseFilmActor()
    {
        $cast = $this->getService()->parseCast($this->traitClass->getCrawlerByLink(
            self::LINK_RU,
            'ru',
            'ru',
            $this->getCookies($this->sweetTvService, 'ru')
        ));
        $this->assertEquals("Сильвестр Сталлоне", ($cast[0])->getName());

        $cast = $this->getService()->parseCast($this->traitClass->getCrawlerByLink(
            self::LINK_UK,
            'uk',
            'uk',
            $this->getCookies($this->sweetTvService, 'uk')
        ));
        $this->assertEquals("Сильвестр Сталлоне", ($cast[0])->getName());

        $cast = $this->getService()->parseCast($this->traitClass->getCrawlerByLink(
            self::LINK_EN,
            'en',
            'en',
            $this->getCookies($this->sweetTvService, 'en')
        ));
        $this->assertEquals("Sylvester Stallone", ($cast[0])->getName());
    }
}
