<?php

namespace App\Tests;

use App\Service\Parsers\Megogo\FilmFieldService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TestUnitMain extends KernelTestCase
{
    protected function setUp(): void
    {
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->clientParser = new Client();
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

    /**
     * @param Crawler|null $crawler
     * @return Crawler
     */
    protected function getNodeFilms(?Crawler $crawler): Crawler
    {
        return $crawler->filter('div.thumbnail div.thumb a');
    }

    /**
     * @return Crawler
     */
    public function getCrawlerByLink($link): Crawler
    {
        $contentHtml = $this->getContentLink($link);

        return $this->getCrawler($contentHtml);
    }

}