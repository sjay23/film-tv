<?php

namespace App\Utility;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DomCrawler\Crawler;

trait CrawlerTrait
{
    public Client $clientParser;

    public function __construct()
    {
        $this->clientParser = new Client();
    }

    /**
     * @param string $link
     * @param string $lang
     * @param string $langDefault
     * @return string|null
     * @throws GuzzleException
     */
    public function getContentLink(string $link, string $lang = 'en', string $langDefault = 'en'): ?string
    {
        sleep(rand(0, 3));
        if ($lang !== $langDefault) {
            $link = str_replace($langDefault, $lang, $link);
        }
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
     * @throws GuzzleException
     */
    public function getCrawlerByLink($link): Crawler
    {
        $contentHtml = $this->getContentLink($link);

        return $this->getCrawler($contentHtml);
    }
}
