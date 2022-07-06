<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\FilmFieldTranslationInput;
use App\DTO\FilmInput;
use App\DTO\AudioInput;
use App\DTO\CastInput;
use App\DTO\CountryInput;
use App\DTO\DirectorInput;
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
            $poster = $node->filter('.movie__item-img > img.img_wauto_hauto')->image()->getUri();
            sleep(rand(0,3));
            $langs = ['en', 'ru', 'uk'];
            foreach ($langs as $lang) {
                $crawlerChild=$this->getCrawlerChild($linkFilm,$lang);
                $filmInput = $this->parseFilmBySweet($filmInput, $crawlerChild, $lang);
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

    private function getCrawlerChild( string $link, string $lang = 'en'):object
    {
        $link = str_replace('en', $lang, $link);
        echo "Parse link: " . $link . "\n";
        $responseChild = $this->client->get($link);
        $htmlChild = (string) $responseChild->getBody();
        $crawlerChild = new Crawler($htmlChild);
        return $crawlerChild;
    }

    private function parseGenre($crawlerChild): ArrayCollection
    {
        $node = $crawlerChild->filter('div.film__genres a');
        $filmGenre = [];
        if ($node->count() !== 0) {
        $filmGenre = $crawlerChild->filter('div.film__genres a')->each(function (Crawler $node){
            $genreInput = new GenreInput($node->text());
            $this->validator->validate($genreInput);
        	return $genreInput;
        });
        }
        return new ArrayCollection($filmGenre);
    }

    private function parseAudio($crawlerChild): ArrayCollection
    {
        $node = $crawlerChild->filter('a.film-audio__link');
        $filmAudio = [];
        if ($node->count() !== 0) {
            $filmAudio = $crawlerChild->filter('div.film__sounds div.film__content a.film-audio__link span')->each(function (Crawler $node) {
                $audioInput = new AudioInput($node->text());
                $this->validator->validate($audioInput);
                return $audioInput;
            });
        }
        return new ArrayCollection ($filmAudio);
    }

    private function parseCast($crawlerChild): ArrayCollection
    {
        $node = $crawlerChild->filter('div.film__actor a');
        $castGenre=[];
        if ($node->count() !== 0) {
            $castGenre = $crawlerChild->filter('div.film__actor a')->each(function (Crawler $node) {
                $castInput = new CastInput($node->text(), $node->link()->getUri());
                $this->validator->validate($castInput);
                return $castInput;
            });
        }
        return new ArrayCollection ($castGenre);
    }

    private function parseCountry($crawlerChild): ArrayCollection
    {
        $filmCountry = $crawlerChild->filter('div.film__countries a.film-left__link')->each(function (Crawler $node){
            $countriesInput = new CountryInput($node->text());
            $this->validator->validate($countriesInput);
            return $countriesInput;
        });
        return new ArrayCollection($filmCountry);
    }

    private function parseDirector($crawlerChild):object
    {
        $node = $crawlerChild->filter('div.film__directors');
        if ($node->count() !== 0) {
            $directorName = $crawlerChild->filter('div.film__directors span')->text();
            $directorLink = $crawlerChild->filter('div.film__directors  a')->link()->getUri();
            $directorInput = new DirectorInput($directorName, $directorLink);
            $this->validator->validate($directorInput);
        }
        return $directorInput;

    }

    private function parseImage($crawlerChild):object
    {
        $imageLink=$crawlerChild->filter('div.film-right  div.film-right__img picture img')->image()->getUri();
            $imageInput = new ImageInput($imageLink);
            $this->validator->validate($imageInput);

        return $imageInput;
    }

    private function parseRating($crawlerChild){
        $node = $crawlerChild->filter('.film__rating');
        if ($node->count() !== 0) {
            $rating = $node->filter('.film-left__details > span')->text();
            }

        return $rating;
    }

    private function getfilmFieldTranslation($crawlerChild, $lang): FilmFieldTranslationInput
    {
        $title = $crawlerChild->filter('.container-fluid_padding li')->last()->text();
        $description = $crawlerChild->filter('p.film-descr__text')->text();
        $banner = $crawlerChild->filter('div.film-right  div.film-right__img picture img')->image()->getUri();
        $filmFieldTranslation = new FilmFieldTranslationInput($title, $description, $banner, $lang);
        $this->validator->validate($filmFieldTranslation);

        return $filmFieldTranslation;
    }

    private function parseFilmBySweet(FilmInput $filmInput, $crawlerChild, string $lang = 'en')
    {
        $movieId = preg_replace("/[^0-9]/", '', $crawlerChild->filter('a.modal__lang-item')->link()->getUri());
        $age = $crawlerChild->filter('div.film__age div.film-left__details div.film-left__flex ')->text();
        $years = $crawlerChild->filter('.film__years > .film-left__details')->text();
        $duration = $this->convertTime($crawlerChild->filter(' span.film-left__time')->text());
        $ratingInput = $this->parseRating($crawlerChild);
        $filmFieldTranslation = $this->getfilmFieldTranslation($crawlerChild,$lang);
        $filmInput->addFilmFieldTranslationInput($filmFieldTranslation);
        $filmInput->setMovieId((int)$movieId);
        $filmInput->setAge((int)$age);
        $filmInput->setRating($ratingInput);
        $filmInput->setYears($years);
        $filmInput->setDuration((int)$duration);

        if ($lang === 'en') {
            $countriesInput = $this->parseCountry($crawlerChild);
            $filmInput->setCountryInput($countriesInput);
            $genreInput = $this->parseGenre($crawlerChild);
            $filmInput->addGenreInput($genreInput);
            $directorInput = $this->parseDirector($crawlerChild);
            $filmInput->addDirectorInput($directorInput);
            $castInput = $this->parseCast($crawlerChild);
            $filmInput->addCastInput($castInput);
            $audioInput = $this->parseAudio($crawlerChild);
            $filmInput->addAudioInput($audioInput);
        }

        sleep(rand(0,3));
        return $filmInput;
    }
}
