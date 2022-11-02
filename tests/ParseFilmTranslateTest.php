<?php

namespace App\Tests;


use App\Service\Parsers\Megogo\FilmFieldTranslateService;

class ParseFilmTranslateTest extends TestUnitMain
{
    public const LINK = 'https://megogo.net/en/view/7585835-crypto.html';

    public function getService()
    {
        return new FilmFieldTranslateService($this->validator);
    }

    public function testParseFilmTranslateTitle()
    {
        $title = $this->getService()->parseTitleTranslate($this->getCrawlerByLink(self::LINK));
        $this->assertEquals("Crypto", $title);
    }

    public function testParseFilmTranslateBannerLink()
    {
        $banner = $this->getService()->parseBannerTranslate($this->getCrawlerByLink(self::LINK));
        $this->assertEquals("https://s9.vcdn.biz/static/f/5123281761/image.jpg/pt/r300x423", $banner->getLink());
    }

    public function testParseFilmTranslateDescription()
    {
        $description = $this->getService()->parseDescriptionTranslate($this->getCrawlerByLink(self::LINK));
        $this->assertEquals("Martin works for a bank in the department that deals with illegal financial transactions. One day, while on a business trip to his native provincial town, the protagonist unexpectedly faces illegal sales of paintings for crypt currency in the local gallery.", $description);
    }

}
