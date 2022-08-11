<?php

declare(strict_types=1);

namespace App\Service\Parsers;

use App\Entity\Provider;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class SweetTvService
 */
class SweetTvService extends MainParserService
{
    protected string $parserName = Provider::SWEET_TV;
    protected string $defaultLink = 'https://sweet.tv/en/movies/all-movies/sort=5';

    /**
     * @return string
     */
    public function getParserName(): string
    {
        return $this->parserName;
    }

    /**
     * @param $linkByFilms
     * @return void
     * @throws GuzzleException
     * @throws Exception
     */
    protected function parserPages($linkByFilms): void
    {
        $html = $this->getContentLink($linkByFilms);
        $crawler = $this->getCrawler($html);
        $pageMax = (int)$crawler->filter('.pagination li')->last()->text();
        $page = 1;
        $this->taskService->setWorkStatus($this->task);
        while ($page <= $pageMax) {
            try {
                $this->parseFilmsByPage($linkByFilms . '/page/$page', $page);
            } catch (Exception $e) {
                $this->taskService->setErrorStatus($this->task, $e->getMessage());
                throw new Exception($e->getMessage());
            }
            $page++;
        }
    }

    /**
     * @param string $linkByFilms
     * @param int $page
     * @return void
     * @throws GuzzleException
     * @throws Exception
     */
    protected function parseFilmsByPage(string $linkByFilms, int $page): void
    {
        $crawler = $this->getPageCrawler($linkByFilms, $page);
        $crawler->filter('.movie__item-link')->each(function ($node) {
            $this->addFilmInput($node);
        });
    }

    /**
     * @param $linkByFilms
     * @param $page
     * @return Crawler
     * @throws GuzzleException
     */
    protected function getPageCrawler($linkByFilms, $page): Crawler
    {
        $link = str_replace('$page', (string)$page, $linkByFilms);
        $html = $this->getContentLink($link);
        return $this->getCrawler($html);
    }
}
