<?php

namespace App\Utility;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
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
     * @param CookieJar|null $cookieJar
     * @return string|null
     * @throws GuzzleException
     */
    public function getContentLink(
        string $link,
        string $lang = 'en',
        string $langDefault = 'en',
        ?CookieJar $cookieJar = null
    ): ?string {
        sleep(rand(0, 3));
        $cookies = ($cookieJar !== null) ? ['cookies' => $cookieJar] : [];
        if ($lang !== $langDefault) {
            $link = str_replace($langDefault, $lang, $link);
        }
        echo 'Parse link: ' . $link . "\n";
        $response = $this->clientParser->get($link, $cookies);

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
    public function getCrawlerByLink(
        string $link,
        string $lang = 'en',
        string $langDefault = 'en',
        ?CookieJar $cookieJar = null
    ): Crawler {
        $contentHtml = $this->getContentLink($link, $lang, $langDefault, $cookieJar);

        return $this->getCrawler($contentHtml);
    }
}
