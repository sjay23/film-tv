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
    public const LINK = 'https://sweet.tv/en/movie/1347-rambo';

    public function getService(): FilmImageService
    {
        return new FilmImageService($this->validator);
    }

    public function getServiceSweetTv(): ?object
    {
        return $this->containerKernel->get(SweetTvService::class);
    }

    /**
     * @throws ReflectionException
     */
    public function getNodePages(Crawler $crawler)
    {
        $object = $this->getServiceSweetTv();
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
        $object = $this->getServiceSweetTv();
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
        $this->assertCount(35, $nodes);
        $images = $this->getService()->parseImage($nodes->first());
        $this->assertStringContainsString("static", ($images[0])->getLink());
    }
}
