<?php

namespace App\Tests\testByService\SweetTvTests;

use App\DTO\ImageInput;
use App\Service\Parsers\SweetTv\FilmFieldTranslateService;
use App\Tests\testByService\TestUnitMain;
use GuzzleHttp\Exception\GuzzleException;
use ReflectionException;

class ParseFilmTranslateTest extends TestUnitMain
{
    public const LINK_UK = 'https://sweet.tv/movie/1347-rambo';
    public const LINK_RU = 'https://sweet.tv/ru/movie/1347-rambo';

    /**
     * @throws GuzzleException
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->crawlerUk = $this->traitClass->getCrawlerByLink(self::LINK_UK);
        $this->crawlerRu = $this->traitClass->getCrawlerByLink(self::LINK_RU);
    }

    public function getService(): FilmFieldTranslateService
    {
        return new FilmFieldTranslateService($this->validator);
    }

    public function testParseFilmTranslateTitle()
    {
        $titleUk = $this->getService()->parseTitleTranslate($this->crawlerUk);
        $titleRu = $this->getService()->parseTitleTranslate($this->crawlerRu);
        $this->assertEquals("Рембо ІV", $titleUk);
        $this->assertEquals("Рембо ІV", $titleRu);
    }

    /**
     * @throws ReflectionException
     */
    public function testParseFilmTranslateBannerLink()
    {
        $bannerUk = $this->getService()->parseBannerTranslate($this->crawlerUk);
        $bannerRU = $this->getService()->parseBannerTranslate($this->crawlerRu);
        $this->assertEquals("https://static.sweet.tv/images/cache/movie_banners/BDBQUEQCOVVSAAQ=/1347-rembo-iv_1280x720.jpg", $bannerUk->getLink());
        $this->assertEquals("https://static.sweet.tv/images/cache/movie_banners/BDBQUEQCOVVSAAQ=/1347-rembo-iv_1280x720.jpg", $bannerRU->getLink());
    }

    public function testParseFilmTranslateDescription()
    {
        $descriptionUk = $this->getService()->parseDescriptionTranslate($this->crawlerUk);
        $descriptionRu = $this->getService()->parseDescriptionTranslate($this->crawlerRu);
        $this->assertEquals("У Таїланді Джон Рембо веде групу найманців річкою Селвін до Бірманського селища, де він нещодавно висадив християнських місіонерів і дотепер від яких не було жодних звісток.", $descriptionUk);
        $this->assertEquals("У Таїланді Джон Рембо веде групу найманців річкою Селвін до Бірманського селища, де він нещодавно висадив християнських місіонерів і дотепер від яких не було жодних звісток.", $descriptionRu);
    }

    /**
     * @throws ReflectionException
     */
    public function testGetImageInput()
    {
        $object = $this->getService();
        $imageInput = $this->invokeMethod(
            $object,
            'getImageInput',
            ['https://s9.vcdn.biz/static/f/5123281761/image.jpg/pt/r300x423']
        );
        $this->assertInstanceOf(ImageInput::class, $imageInput);
        $this->assertEquals(
            'https://s9.vcdn.biz/static/f/5123281761/image.jpg/pt/r300x423',
            $imageInput->getLink()
        );
    }
}
