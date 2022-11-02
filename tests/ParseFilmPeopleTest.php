<?php

namespace App\Tests;

use App\Service\Parsers\Megogo\FilmPeopleService;

class ParseFilmPeopleTest extends TestUnitMain
{
    public const LINK = 'https://megogo.net/en/view/7585835-crypto.html';
    public const LINK_PAGE = 'https://megogo.net/en/view/7585835-crypto.html?video_view_tab=cast';

    public function getService()
    {
        return new FilmPeopleService($this->validator);
    }

    public function testParseFilmDirector()
    {
        $cast = $this->getService()->parseDirector($this->traitClass->getCrawlerByLink(self::LINK));
        $this->assertEquals("John Stalberg Jr.", ($cast[0])->getName());
    }

    public function testParseFilmActor()
    {
        $contentHtml = $this->traitClass->getContentLink(self::LINK_PAGE);
        $this->traitClass->getCrawler($contentHtml);
        $cast = $this->getService()->parseCast($this->traitClass->getCrawler($contentHtml));
        $this->assertEquals("Beau Knapp", ($cast[0])->getName());
    }
}
