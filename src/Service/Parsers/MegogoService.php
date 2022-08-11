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

    /**
     * @return void
     * @throws GuzzleException
     * @throws Exception
     */
    protected function parserPages(): void
    {
        try {
            $this->parseFilmsByPage($this->getDefaultLink());
        } catch (Exception $e) {
            $this->taskService->setErrorStatus($this->task, $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param string $linkByFilms
     * @return void
     * @throws GuzzleException
     * @throws Exception
     */
    protected function parseFilmsByPage(string $linkByFilms): void
    {
        $crawler = $this->getPageCrawler($linkByFilms);
        $crawler->filter('div.thumbnail div.thumb a')->each(function ($node) {

            if (
                !str_contains($node->link()->getUri(), 'treyler')
                and !str_contains($node->link()->getUri(), 'trailer')
            ) {
                $this->addFilmInput($node);
            }
        });
        $this->parseFilmsByPage($this->getNextPageLink($this->getNextPageToken($crawler)));
    }

    /**
     * @param $crawler
     * @return string
     */
    private function getNextPageToken($crawler): string
    {
        return $crawler->filter('div.pagination-more a.link-gray ')->attr('data-page-more');
    }

    /**
     * @param $linkByFilms
     * @param null $page
     * @return Crawler
     * @throws GuzzleException
     */
    protected function getPageCrawler($linkByFilms, $page = null): Crawler
    {
        $html = $this->getContentLink($linkByFilms);
        if ($linkByFilms === $this->getDefaultLink()) {
            $html = str_replace('\"', '', $html);
        }
        return $this->getCrawler($html);
    }

    /**
     * @param $nextPageToken
     * @return string
     */
    private function getNextPageLink($nextPageToken): string
    {
        return str_replace('TOKEN', $nextPageToken, $this->getDefaultLink());
    }
}
