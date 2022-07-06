<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\FilmFieldTranslationInput;
use App\DTO\FilmInput;
use App\DTO\AudioInput;
use App\DTO\CountryInput;
use App\DTO\PeopleInput;
use App\DTO\GenreInput;
use App\DTO\ImageInput;
use Doctrine\Common\Collections\ArrayCollection;
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

    /**
     * @throws GuzzleException
     */
    public function exec()
    {
        $linkByFilms = 'https://sweet.tv/en/movies/all-movies/sort=5';
        $html = $this->getContentLink($linkByFilms);
        $crawler = $this->getCrawler($html);

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

            sleep(rand(0,3));
            $langs = ['en', 'ru', 'uk'];
            foreach ($langs as $lang) {
                $htmlChild = $this->getContentLink($linkFilm, $lang);
                $crawlerChild = $this->getCrawler($htmlChild);
                $filmInput = $this->parseFilmBySweet($filmInput, $crawlerChild, $lang);
            }
            $posterInput = $this->parseImage($node);
            $filmInput->addImageInput($posterInput);
            dump($filmInput);die();
            return $filmInput;
        });
        return $filmsData;
    }


    private function parseFilmBySweet(FilmInput $filmInput, $crawlerChild, string $lang = 'en'): FilmInput
    {
        $filmFieldTranslation = $this->getFilmFieldTranslation($crawlerChild,$lang);
        $filmInput->addFilmFieldTranslationInput($filmFieldTranslation);

        if ($lang === 'en') {
            $movieId = preg_replace("/[^0-9]/", '', $crawlerChild->filter('a.modal__lang-item')->link()->getUri());
            $age = $crawlerChild->filter('div.film__age div.film-left__details div.film-left__flex ')->text();
            $years = $crawlerChild->filter('.film__years > .film-left__details')->text();
            $duration = $this->convertTime($crawlerChild->filter(' span.film-left__time')->text());
            $rating = $this->parseRating($crawlerChild);
            $filmInput->setMovieId((int)$movieId);
            $filmInput->setAge($age);
            $filmInput->setRating($rating);
            $filmInput->setYears((int)$years);
            $filmInput->setDuration((int)$duration);

            $countriesInput = $this->parseCountry($crawlerChild);
            $filmInput->setCountriesInput($countriesInput);
            $genreInput = $this->parseGenre($crawlerChild);
            $filmInput->setGenresInput($genreInput);
            $directorInput = $this->parseDirector($crawlerChild);
            $filmInput->setDirectorsInput($directorInput);
            $castInput = $this->parseCast($crawlerChild);
            $filmInput->setCastsInput($castInput);
            $audioInput = $this->parseAudio($crawlerChild);
            $filmInput->setAudiosInput($audioInput);
        }

        sleep(rand(0,3));
        return $filmInput;
    }

    private function convertTime(string $str){
        $a = preg_replace("/[^0-9]/", '', $str);
        $time = ((substr($a,0,2))*60)+((substr($a,-2,2)));
        return $time;
    }

    private function getCrawler(string $html): Crawler
    {
        return new Crawler($html);
    }

    /**
     * @throws GuzzleException
     */
    private function getContentLink(string $link, string $lang = 'en'): ?string
    {
        if ($lang !== 'en') {
            $link = str_replace('en', $lang, $link);
        }
        echo "Parse link: " . $link . "\n";
        $response = $this->client->get($link);

        return (string) $response->getBody();
    }

    private function parseGenre($crawler): ArrayCollection
    {
        $node = $crawler->filter('div.film__genres a');
        $filmGenre = [];
        if ($node->count() !== 0) {
        $filmGenre = $crawler->filter('div.film__genres a')->each(function (Crawler $node){
            $genreInput = new GenreInput($node->text());
            $this->validator->validate($genreInput);
        	return $genreInput;
        });
        }
        return new ArrayCollection($filmGenre);
    }

    private function parseAudio($crawler): ArrayCollection
    {
        $node = $crawler->filter('a.film-audio__link');
        $filmAudio = [];
        if ($node->count() !== 0) {
            $filmAudio = $crawler->filter('div.film__sounds div.film__content a.film-audio__link span')->each(function (Crawler $node) {
                $audioInput = new AudioInput($node->text());
                $this->validator->validate($audioInput);
                return $audioInput;
            });
        }
        return new ArrayCollection ($filmAudio);
    }

    private function parseCast($crawler): ArrayCollection
    {
        $node = $crawler->filter('div.film__actor a');
        $castGenre=[];
        if ($node->count() !== 0) {
            $castGenre = $crawler->filter('div.film__actor a')->each(function (Crawler $node) {
                $castInput = new PeopleInput($node->text(), $node->link()->getUri());
                $this->validator->validate($castInput);
                return $castInput;
            });
        }
        return new ArrayCollection ($castGenre);
    }

    private function parseCountry($crawler): ArrayCollection
    {
        $filmCountry = $crawler->filter('div.film__countries a.film-left__link')->each(function (Crawler $node){
            $countriesInput = new CountryInput($node->text());
            $this->validator->validate($countriesInput);
            return $countriesInput;
        });
        return new ArrayCollection($filmCountry);
    }

    private function parseDirector($crawler): ArrayCollection
    {
        $node = $crawler->filter('div.film__directors');
        $directors = [];
        if ($node->count() !== 0) {
            $directorName = $crawler->filter('div.film__directors span')->text();
            $directorLink = $crawler->filter('div.film__directors  a')->link()->getUri();
            $directorInput = new PeopleInput($directorName, $directorLink);
            $this->validator->validate($directorInput);
            $directors[] = $directorInput;
        }
        return new ArrayCollection($directors);
    }

    private function parseImage($crawler): ImageInput
    {
        $imageLink = $crawler->filter('.movie__item-img > img.img_wauto_hauto')->image()->getUri();
        $imageInput = new ImageInput($imageLink);
        $this->validator->validate($imageInput);

        return $imageInput;
    }

    private function parseRating($crawler): ?string
    {
        $rating = null;
        $node = $crawler->filter('.film__rating');
        if ($node->count() !== 0) {
            $rating = $node->filter('.film-left__details > span')->text();
        }

        return $rating;
    }

    private function getFilmFieldTranslation($crawlerChild, $lang): FilmFieldTranslationInput
    {
        $title = $crawlerChild->filter('.container-fluid_padding li')->last()->text();
        $description = $crawlerChild->filter('p.film-descr__text')->text();
        $banner = $crawlerChild->filter('div.film-right  div.film-right__img picture img')->image()->getUri();
        $filmFieldTranslation = new FilmFieldTranslationInput($title, $description, $banner, $lang);
        $this->validator->validate($filmFieldTranslation);

        return $filmFieldTranslation;
    }
}
