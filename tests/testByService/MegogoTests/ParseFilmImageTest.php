<?php

namespace App\Tests\testByService\MegogoTests;

use App\Service\Parsers\Megogo\FilmImageService;
use App\Service\Parsers\MegogoService;
use App\Tests\testByService\TestUnitMain;
use GuzzleHttp\Exception\GuzzleException;
use ReflectionException;
use Symfony\Component\DomCrawler\Crawler;

class ParseFilmImageTest extends TestUnitMain
{
    public const LINK_EN = 'https://megogo.net/en/search-extended?category_id=16&main_tab=filters&sort=add&ajax=true&origin=/en/search-extended?category_id=16&main_tab=filters&sort=add&widget=widget_58';
    public const LINK_RU = 'https://megogo.net/ru/search-extended?category_id=16&main_tab=filters&sort=add&ajax=true&origin=/ru/search-extended?category_id=16&main_tab=filters&sort=add&widget=widget_58';
    public const LINK_UK = 'https://megogo.net/uk/search-extended?category_id=16&main_tab=filters&sort=add&ajax=true&origin=/uk/search-extended?category_id=16&main_tab=filters&sort=add&widget=widget_58';

    public function getService(): FilmImageService
    {
        return new FilmImageService($this->validator);
    }

    /**
     * @throws ReflectionException
     */
    public function getNodePages(Crawler $crawler)
    {
        $object = $this->megogoService;
        return $this->invokeMethod(
            $object,
            'getNodeFilms',
            [$crawler]
        );
    }

    /**
     * @throws ReflectionException
     */
    public function getPageCrawler(string $link)
    {
        $object = $this->megogoService;
        return $this->invokeMethod(
            $object,
            'getPageCrawler',
            [$link]
        );
    }

    /**
     * @throws GuzzleException|ReflectionException
     */
    public function testParseFilmImage()
    {
        $images_en = $this->getImageByLanguage(self::LINK_EN);
        $images_ru = $this->getImageByLanguage(self::LINK_RU);
        $images_uk = $this->getImageByLanguage(self::LINK_UK);
        $this->assertStringContainsString("static", ($images_en[0])->getLink());
        $this->assertStringContainsString("static", ($images_ru[0])->getLink());
        $this->assertStringContainsString("static", ($images_uk[0])->getLink());
    }

    public function getImageByLanguage($language)
    {
        $crawler = $this->getPageCrawler($language);
        $nodes = $this->getNodePages($crawler);
        $this->assertCount(30, $nodes);
        return $this->getService()->parseImage($nodes->first());
    }
}
