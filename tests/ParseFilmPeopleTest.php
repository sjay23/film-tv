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
        $cast = $this->getService()->parseDirector($this->getCrawlerByLink(self::LINK));
        $this->assertEquals("John Stalberg Jr.", ($cast[0])->getName());
    }

    public function testParseFilmActor()
    {
        $contentHtml = $this->getContentLink(self::LINK_PAGE);
        $this->getCrawler($contentHtml);
        $cast = $this->getService()->parseCast($this->getCrawler($contentHtml));
        $this->assertEquals("Beau Knapp", ($cast[0])->getName());
    }
}
