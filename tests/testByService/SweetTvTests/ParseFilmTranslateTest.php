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
    public const LINK_EN = 'https://sweet.tv/en/movie/1347-rambo';

    /**
     * @throws GuzzleException
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->crawlerUk = $this->traitClass->getCrawlerByLink(
            self::LINK_UK,
            'uk',
            'uk',
            $this->getCookies($this->sweetTvService, 'uk')
        );
        $this->crawlerRu = $this->traitClass->getCrawlerByLink(
            self::LINK_RU,
            'ru',
            'ru',
            $this->getCookies($this->sweetTvService, 'ru')
        );
        $this->crawlerEn = $this->traitClass->getCrawlerByLink(
            self::LINK_EN,
            'en',
            'en',
            $this->getCookies($this->sweetTvService, 'en')
        );
    }

    public function getService(): FilmFieldTranslateService
    {
        return new FilmFieldTranslateService($this->validator);
    }

    public function testParseFilmTranslateTitle()
    {
        $titleUk = $this->getService()->parseTitleTranslate($this->crawlerUk);
        $titleRu = $this->getService()->parseTitleTranslate($this->crawlerRu);
        $titleEn = $this->getService()->parseTitleTranslate($this->crawlerEn);
        $this->assertEquals("Рембо ІV", $titleUk);
        $this->assertEquals("Рэмбо IV", $titleRu);
        $this->assertEquals("Rambo", $titleEn);
    }

    /**
     * @throws ReflectionException
     */
    public function testParseFilmTranslateBannerLink()
    {
        $bannerUk = $this->getService()->parseBannerTranslate($this->crawlerUk);
        $bannerRU = $this->getService()->parseBannerTranslate($this->crawlerRu);
        $bannerEn = $this->getService()->parseBannerTranslate($this->crawlerEn);
        $this->assertEquals(
            'https://static.sweet.tv/images/cache/movie_banners/BDBQUEQCOVVSAAQ=/1347-rembo-iv_1280x720.jpg',
            $bannerUk->getLink()
        );
        $this->assertEquals(
            'https://static.sweet.tv/images/cache/movie_banners/BDBQUEQCOJ2SAAQ=/1347-rembo-iv_1280x720.jpg',
            $bannerRU->getLink()
        );
        $this->assertEquals(
            'https://static.sweet.tv/images/cache/movie_banners/BDBQUEQCMVXCAAQ=/1347-rembo-iv_1280x720.jpg',
            $bannerEn->getLink()
        );
    }

    public function testParseFilmTranslateDescription()
    {
        $descriptionUk = $this->getService()->parseDescriptionTranslate($this->crawlerUk);
        $descriptionRu = $this->getService()->parseDescriptionTranslate($this->crawlerRu);
        $descriptionEn = $this->getService()->parseDescriptionTranslate($this->crawlerEn);
        $this->assertEquals(
            'У Таїланді Джон Рембо веде групу найманців річкою Селвін до Бірманського селища, де він нещодавно висадив християнських місіонерів і дотепер від яких не було жодних звісток.',
            $descriptionUk
        );
        $this->assertEquals(
            'Вьетнамский ветеран Джон Рэмбо ведет уединенный образ жизни на окраине Бангкока. Уставший от борьбы и кровопролития, скрываясь от проблем, он селится в небольшом доме у реки и проводит дни, ремонтируя старые лодки и катера. Однако судьба вновь заставляет его взяться за оружие. Собрав отряд из пяти наемников, Рэмбо встает на защиту жителей тайской деревни и американских миссионеров, которых захватили в заложники бирманские боевики.',
            $descriptionRu
        );
        $this->assertEquals(
            'When governments fail to act on behalf of captive missionaries, ex-Green Beret John James Rambo sets aside his peaceful existence along the Salween River in a war-torn region of Thailand to take action. Although he\'s still haunted by violent memories of his time as a U.S. soldier during the Vietnam War, Rambo can hardly turn his back on the aid workers who so desperately need his help.',
            $descriptionEn
        );
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
