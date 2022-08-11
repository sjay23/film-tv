<?php

namespace App\Utility;

use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DomCrawler\Crawler;

trait CrawlerTrait
{
    /**
     * @param string $link
     * @param string $lang
     * @return string|null
     * @throws GuzzleException
     */
    public function getContentLink(string $link, string $lang = 'en'): ?string
    {
        sleep(rand(0, 3));
        if ($lang !== self::LANG_DEFAULT) {
            $link = str_replace(self::LANG_DEFAULT, $lang, $link);
        }
        echo 'Parse link: ' . $link . "\n";
        $response = $this->client->get($link);

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