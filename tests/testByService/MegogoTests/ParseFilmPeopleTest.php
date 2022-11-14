<?php

namespace App\Tests\testByService\MegogoTests;

use App\Service\Parsers\Megogo\FilmPeopleService;
use App\Tests\testByService\TestUnitMain;
use GuzzleHttp\Exception\GuzzleException;

class ParseFilmPeopleTest extends TestUnitMain
{
    public const LINK_EN = 'https://megogo.net/en/view/7585835-crypto.html';
    public const LINK_RU = 'https://megogo.net/ru/view/7585835-crypto.html';
    public const LINK_UK = 'https://megogo.net/uk/view/7585835-crypto.html';
    public const LINK_PAGE = 'https://megogo.net/en/view/7585835-crypto.html?video_view_tab=cast';
    public const LINK_PAGE_RU = 'https://megogo.net/ru/view/7585835-crypto.html?video_view_tab=cast';
    public const LINK_PAGE_UK = 'https://megogo.net/uk/view/7585835-crypto.html?video_view_tab=cast';

    public function getService()
    {
        return new FilmPeopleService($this->validator);
    }

    /**
     * @throws GuzzleException
     */
    public function testParseFilmDirector()
    {
        $cast_en = $this->getService()->parseDirector($this->traitClass->getCrawlerByLink(self::LINK_EN));
        $cast_ru = $this->getService()->parseDirector($this->traitClass->getCrawlerByLink(self::LINK_RU));
        $cast_uk = $this->getService()->parseDirector($this->traitClass->getCrawlerByLink(self::LINK_UK));
        $this->assertEquals("John Stalberg Jr.", ($cast_en[0])->getName());
        $this->assertEquals("Джон Сталберг", ($cast_ru[0])->getName());
        $this->assertEquals("Джон Сталберг", ($cast_uk[0])->getName());
    }

    public function testParseFilmActor()
    {
        $cast_en = $this->getCastByLanguage(self::LINK_PAGE);
        $cast_ru = $this->getCastByLanguage(self::LINK_PAGE_RU);
        $cast_uk = $this->getCastByLanguage(self::LINK_PAGE_UK);
        $this->assertEquals("Beau Knapp", ($cast_en[0])->getName());
        $this->assertEquals("Бо Напп", ($cast_ru[0])->getName());
        $this->assertEquals("Бо Напп", ($cast_uk[0])->getName());
    }

    public function getCastByLanguage($language)
    {
        $contentHtml = $this->traitClass->getContentLink($language);
        $this->traitClass->getCrawler($contentHtml);
        return $this->getService()->parseCast($this->traitClass->getCrawler($contentHtml));
    }
}
