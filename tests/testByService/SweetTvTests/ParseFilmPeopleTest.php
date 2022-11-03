<?php

namespace App\Tests\testByService\SweetTvTests;

use App\Service\Parsers\SweetTv\FilmPeopleService;
use App\Tests\testByService\TestUnitMain;

class ParseFilmPeopleTest extends TestUnitMain
{
    public const LINK = 'https://sweet.tv/ru/movie/1347-rambo';

    public function getService()
    {
        return new FilmPeopleService($this->validator);
    }

    public function testParseFilmDirector()
    {
        $cast = $this->getService()->parseDirector($this->traitClass->getCrawlerByLink(self::LINK));
        $this->assertEquals("Сильвестр Сталлоне", ($cast[0])->getName());
    }

    public function testParseFilmActor()
    {
        $contentHtml = $this->traitClass->getContentLink(self::LINK);
        $this->traitClass->getCrawler($contentHtml);
        $cast = $this->getService()->parseCast($this->traitClass->getCrawler($contentHtml));
        $this->assertEquals("Сильвестр Сталлоне", ($cast[0])->getName());
    }
}
