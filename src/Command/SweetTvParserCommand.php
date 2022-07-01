<?php

namespace App\Command;

use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class SweetTvParserCommand extends Command
{
    protected static $defaultName = 'app:sweet-tv-parser';

    /**
     * @var Client
     */
    private Client $client;

    public function __construct() {
        $this->client = new Client();
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    /**
     * @throws GuzzleException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Start parser Sweet tv',
            '============',
            '',
        ]);
        $linkByFilms = 'https://sweet.tv/en/movies/all-movies/sort=5';
        $response = $this->client->get($linkByFilms);
        $html = (string) $response->getBody();

        $crawler = new Crawler($html);
        $pageMax = (int) $crawler->filter('.pagination li')->last()->text();

        $page = 1;
        while ($page <= $pageMax) {
            $filmsData = $this->parseFilmsByPage($linkByFilms . '/page/$page', $page);
            dump($filmsData);
            die();
        }

        die();

        return Command::SUCCESS;
    }

    /**
     * @throws GuzzleException
     */
    private function parseFilmsByPage(string $link, int $page): array
    {
        $link = str_replace('$page', $page, $link);
        $response = $this->client->get($link);
        echo "Parse link: " . $link . "\n";
        $html = (string) $response->getBody();
        $crawler = new Crawler($html);
        $filmsData = $crawler->filter('.movie__item-link')->each(function ($node) {
            $filmData = [];
            $linkFilm = $node->link()->getUri();
            $filmData['link'] = $linkFilm;
            $filmData['id'] = $node->attr('data-movie-id');
            $filmData['poster']['en'] = $node->filter('.movie__item-img > img.img_wauto_hauto')->image()->getUri();
            sleep(rand(0,3));
            $filmDataEn = $this->parseFilmBySweet($linkFilm);
            $filmDataRu = $this->parseFilmBySweet($linkFilm, 'ru');
            $filmDataUk = $this->parseFilmBySweet($linkFilm, 'uk');
            return array_merge_recursive($filmData, $filmDataEn, $filmDataRu, $filmDataUk);
        });
        return $filmsData;
    }

    private function convertTime(string $str){
        $a=preg_replace("/[^0-9]/", '', $str);
        $time= ((substr($a,0,2))*60)+((substr($a,-2,2)));
        return $time;
    }

    private function parseFilmBySweet(string $link, string $lang = 'en'): array
    {

        $link = str_replace('en', $lang, $link);
        $filmData = [];
        $responseChild = $this->client->get($link);
        echo "Parse link: " . $link . "\n";
        $htmlChild = (string) $responseChild->getBody();
        $crawlerChild = new Crawler($htmlChild);
        $filmData['id'] = preg_replace("/[^0-9]/", '', $crawlerChild->filter('a.modal__lang-item')->link()->getUri());
        $filmData['title'][$lang] = $crawlerChild->filter('.container-fluid_padding li')->last()->text();
        $filmData['description'][$lang] = $crawlerChild->filter('p.film-descr__text')->text();
        $filmData['banner'][$lang] = $crawlerChild->filter('div.film-right  div.film-right__img picture img')->image()->getUri();

        if ($lang === 'en') {
            $filmData['year'] = $crawlerChild->filter('.film__years > .film-left__details')->text();

            $ratingNode = $crawlerChild->filter('.film__rating');
            if ($ratingNode->count() !== 0) {
                $filmData['rating'] = $ratingNode->filter('.film-left__details > span')->text();
            }

            $filmData['country'] = $crawlerChild->filter('div.film__countries a.film-left__link')->text();
            $genreNode = $crawlerChild->filter('div.film__genres a');
            if($genreNode->count() !== 0) {
                $filmData['genre'] = $genreNode->each(function (Crawler $node) {
                    return $node->text();
                });
            }
            $directorNode = $crawlerChild->filter('div.film__directors');
            if ($directorNode->count() !== 0) {
                $filmData['director']['name'] = $directorNode->filter('span')->text();
                $filmData['director']['link'] = $directorNode->filter('a')->link()->getUri();
            }
            $castNode = $crawlerChild->filter('div.film__actor a');
            if ($castNode->count() !== 0) {
                $filmData['cast'] = $castNode->each(function (Crawler $node) {
                    return [
                        'name' => $node->text(),
                        'link' => $node->link()->getUri()
                    ];
                });
            }
            $filmData['age'] = $crawlerChild->filter('div.film__age div.film-left__details div.film-left__flex ')->text();
            $filmData['duration'] = $this->convertTime($crawlerChild->filter(' span.film-left__time')->text());

            $audioNode = $crawlerChild->filter('a.film-audio__link');
            if($audioNode->count() !== 0) {
                $filmData['audio'] = $audioNode->each(function (
                    Crawler $node
                ) {
                    return $node->attr('title');
                });
            }
        }
        sleep(rand(0,3));
        dump($filmData);
        return $filmData;
    }
}