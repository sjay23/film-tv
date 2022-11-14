<?php

namespace App\Tests\testByService\SweetTvTests;

use App\Service\Parsers\SweetTv\FilmImageService;
use App\Service\Parsers\SweetTvService;
use App\Tests\testByService\TestUnitMain;
use GuzzleHttp\Exception\GuzzleException;
use ReflectionException;
use Symfony\Component\DomCrawler\Crawler;

class ParseFilmImageTest extends TestUnitMain
{
    public const LINK_EN = 'https://sweet.tv/en/movies/all-movies/sort=5';
    public const LINK_RU = 'https://sweet.tv/ru/movies/all-movies/sort=5';
    public const LINK_UK = 'https://sweet.tv/uk/movies/all-movies/sort=5';

    public function getService(): FilmImageService
    {
        return new FilmImageService($this->validator);
    }

    /**
     * @throws ReflectionException
     */
    public function getNodePages(Crawler $crawler)
    {
        $object = $this->sweetTvService;
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
        $object = $this->sweetTvService;
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
