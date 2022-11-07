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
    public const LINK = 'https://sweet.tv/en/movies/all-movies/sort=5';

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
        $crawler = $this->getPageCrawler(self::LINK);
        $nodes = $this->getNodePages($crawler);
        $this->assertCount(30, $nodes);
        $images = $this->getService()->parseImage($nodes->first());
        $this->assertStringContainsString("static", ($images[0])->getLink());
    }
}
