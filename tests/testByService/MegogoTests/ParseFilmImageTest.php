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
    public const LINK = 'https://megogo.net/en/search-extended?category_id=16&main_tab=filters&sort=add&ajax=true&origin=/en/search-extended?category_id=16&main_tab=filters&sort=add&widget=widget_58';

    public function getService(): FilmImageService
    {
        return new FilmImageService($this->validator);
    }

    public function getServiceMegogo(): ?object
    {
        return $this->containerKernel->get(MegogoService::class);
    }

    /**
     * @throws ReflectionException
     */
    public function getNodePages(Crawler $crawler)
    {
        $object = $this->getServiceMegogo();
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
        $object = $this->getServiceMegogo();
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
