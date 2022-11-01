<?php

namespace App\Tests;

use App\Service\Parsers\Megogo\FilmFieldService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ParseFilmTest extends KernelTestCase
{
    protected function setUp(): void
    {
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->clientParser = new Client();
    }

    public function testParseFilm()
    {
        $link = 'https://megogo.net/en/view/7585835-crypto.html';
        $filmFieldService = new FilmFieldService($this->validator);
        $contentHtml = $this->getContentLink($link);
        $crawler = $this->getCrawler($contentHtml);
        $age = $filmFieldService->parseAge($crawler);

        $filmId = $filmFieldService->parseFilmId($link);

        $this->assertEquals("7585835", $filmId);
        $this->assertEquals("18+", $age);
    }


    /**
     * @param string $link
     * @return string|null
     * @throws GuzzleException
     */
    public function getContentLink(string $link): ?string
    {
        sleep(rand(0, 3));
        echo 'Parse link: ' . $link . "\n";
        $response = $this->clientParser->get($link);

        return (string)$response->getBody();
    }

    /**
     * @param string $html
     * @return Crawler
     */
    public function getCrawler(string $html): Crawler
    {
        return new Crawler($html);
    }
}
