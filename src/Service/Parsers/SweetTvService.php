<?php

declare(strict_types=1);

namespace App\Service\Parsers;

use App\Entity\Provider;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class SweetTvService
 */
class SweetTvService extends MainParserService
{
    protected string $parserName = Provider::SWEET_TV;
    protected string $defaultLink = 'https://sweet.tv/en/movies/all-movies/sort=5';
    protected int $countFilmsOnPage = 30;
    protected string $firstPage = '1';

    /**
     * @param Crawler|null $crawler
     * @return Crawler
     */
    protected function getNodeFilms(?Crawler $crawler): Crawler
    {
        return $crawler->filter('.movie__item-link');
    }

    /**
     * @param Crawler|null $crawler
     * @param string|null $previousPage
     * @return string
     */
    protected function getNextPageToken(?Crawler $crawler, ?string $previousPage = null): string
    {
        $pageInt = intval($previousPage);
        $pageInt++;
        return (string) $pageInt;
    }

    /**
     * @param $linkByFilms
     * @return Crawler
     * @throws GuzzleException
     */
    protected function getPageCrawler($linkByFilms): Crawler
    {
        $html = $this->getContentLink($linkByFilms);
        return $this->getCrawler($html);
    }

    /**
     * @param string $nextPageToken
     * @return string
     * @throws Exception
     */
    protected function getNextPageLink(string $nextPageToken): string
    {
        return str_replace('$page', $nextPageToken, $this->getDefaultLink() . '/page/$page');
    }
}
