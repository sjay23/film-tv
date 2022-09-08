<?php

declare(strict_types=1);

namespace App\Service\Parsers;

use App\Entity\Provider;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class MegogoService
 */
class MegogoService extends MainParserService
{
    protected string $parserName = Provider::MEGOGO;
    protected string $defaultLink = 'https://megogo.net/en/search-extended?category_id=16&main_tab=filters&sort=add&ajax=true&origin=/en/search-extended?category_id=16&main_tab=filters&sort=add&widget=widget_58';
    protected int $countFilmsOnPage = 30;
    protected string $firstPage = '';

    protected function isItemFilm(Crawler $node): bool
    {
        return (
            !str_contains($node->link()->getUri(), 'treyler')
            and !str_contains($node->link()->getUri(), 'trailer')
        );
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
     * @param Crawler|null $crawler
     * @param string|null $previousPage
     * @return string
     */
    protected function getNextPageToken(?Crawler $crawler, ?string $previousPage = null): string
    {
        return $crawler->filter('div.pagination-more a.link-gray ')->attr('data-page-more');
    }

    /**
     * @param string $linkByFilms
     * @return Crawler
     * @throws GuzzleException
     */
    protected function getPageCrawler(string $linkByFilms): Crawler
    {
        $html = $this->getContentLink($linkByFilms);
        $htmlJson  = json_decode($html);
        $html = $htmlJson->data->widgets->widget_58->html;

        return $this->getCrawler($html);
    }

    /**
     * @param string $nextPageToken
     * @return string
     * @throws Exception
     */
    protected function getNextPageLink(string $nextPageToken): string
    {
        return str_replace('TOKEN', $nextPageToken, $this->getDefaultLink() . '&pageToken=TOKEN');
    }
}
