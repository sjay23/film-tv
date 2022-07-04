<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\FilmFieldTranslationInput;
use App\DTO\FilmInput;
use App\Entity\FilmByProvider;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class SweetTvService
 */
class SweetTvService
{
    /**
     * @var Client
     */
    private Client $client;

    /**
     * @var ValidatorInterface
     */
    private ValidatorInterface $validator;

    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(
        ValidatorInterface $validator
    )
    {
        $this->validator = $validator;
        $this->client = new Client();
    }

    public function exec()
    {
        $linkByFilms = 'https://sweet.tv/en/movies/all-movies/sort=5';
        $response = $this->client->get($linkByFilms);
        $html = (string) $response->getBody();

        $crawler = new Crawler($html);
        $pageMax = (int) $crawler->filter('.pagination li')->last()->text();

        $page = 1;
        while ($page <= $pageMax) {
            sleep(rand(0,3));
            $filmsData = $this->parseFilmsByPage($linkByFilms . '/page/$page', $page);
            dump($filmsData);
            die();
        }
    }

    /**
     * @throws GuzzleException
     */
    private function parseFilmsByPage(string $link, int $page): array
    {
        $page = $page . ''; //переделать в строку
        $link = str_replace('$page', $page, $link);
        echo "Parse link: " . $link . "\n";
        $response = $this->client->get($link);
        $html = (string) $response->getBody();
        $crawler = new Crawler($html);
        $filmsData = $crawler->filter('.movie__item-link')->each(function ($node) {
            $filmInput = new FilmInput();
            $linkFilm = $node->link()->getUri();
            $filmInput->setLink($linkFilm);
            $id = $node->attr('data-movie-id');
            $poster = $node->filter('.movie__item-img > img.img_wauto_hauto')->image()->getUri();
            sleep(rand(0,3));
            $langs = ['en', 'ru', 'uk'];
            foreach ($langs as $lang) {
                $filmInput = $this->parseFilmBySweet($filmInput, $linkFilm, $lang);
            }
            return $filmInput;
        });
        return $filmsData;
    }

    private function convertTime(string $str){
        $a = preg_replace("/[^0-9]/", '', $str);
        $time = ((substr($a,0,2))*60)+((substr($a,-2,2)));
        return $time;
    }

    private function parseFilmBySweet(FilmInput $filmInput, string $link, string $lang = 'en'): array
    {
        $link = str_replace('en', $lang, $link);
        $filmData = [];
        echo "Parse link: " . $link . "\n";
        $responseChild = $this->client->get($link);
        $htmlChild = (string) $responseChild->getBody();
        $crawlerChild = new Crawler($htmlChild);
        $filmData['id'] = preg_replace("/[^0-9]/", '', $crawlerChild->filter('a.modal__lang-item')->link()->getUri());
        
        $title = $crawlerChild->filter('.container-fluid_padding li')->last()->text();
        $description = $crawlerChild->filter('p.film-descr__text')->text();
        $banner = $crawlerChild->filter('div.film-right  div.film-right__img picture img')->image()->getUri();
        $filmFieldTranslation = new FilmFieldTranslationInput($title, $description, $banner, $lang);
        $this->validator->validate($filmFieldTranslation);
        $filmInput->addFilmFieldTranslationInput($filmFieldTranslation);
        die();
        if ($lang === 'en') {
            $age =  $crawlerChild->filter('div.film__age div.film-left__details div.film-left__flex ')->text();
            $filmInput->setAge((int)$age);
            $filmData['year'] = $crawlerChild->filter('.film__years > .film-left__details')->text();
            $filmData['rating'] = $crawlerChild->filter('.film__rating > .film-left__details > span')->text();
            $filmData['country'] = $crawlerChild->filter('div.film__countries a.film-left__link')->text();
            $filmData['genre'] = $crawlerChild->filter('div.film__genres a:nth-child(2)')->text();
            $filmData['director']['name'] = $crawlerChild->filter('div.film__directors span')->text();
            $filmData['director']['link'] = $crawlerChild->filter('div.film__directors  a')->link()->getUri();
            $filmData['cast'] = $crawlerChild->filter('div.film__actor a')->each(function (Crawler $node) {
                return [ 'name' => $node->text(), 'link' => $node->link()->getUri() ];});

            $filmData['duration'] = $crawlerChild->filter(' span.film-left__time')->text();
            $filmData['banner']['en'] = $crawlerChild->filter('div.film-right  div.film-right__img picture img')->image()->getUri();
            $filmData['audio'] =  array_unique($crawlerChild->filter('div.film__sounds div.film__content a.film-audio__link span')->each(function (Crawler $node) {

                return $node->text();
            }));

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
