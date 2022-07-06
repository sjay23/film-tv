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

    private function getCrawlerChild( string $link, string $lang = 'en'){
        $link = str_replace('en', $lang, $link);
        echo "Parse link: " . $link . "\n";
        $responseChild = $this->client->get($link);
        $htmlChild = (string) $responseChild->getBody();
        $crawlerChild = new Crawler($htmlChild);
        return $crawlerChild;
    }

    private function parseGenre($crawlerChild) {
        $filmGenre = $crawlerChild->filter('div.film__genres a')->each(function (Crawler $node){
            $genreInput = new GenreInput($node->text());
            $this->validator->validate($genreInput);
        	return $genreInput;
        });
        return $filmGenre;
    }

    private function parseAudio($crawlerChild){
        $filmAudio = $crawlerChild->filter('div.film__sounds div.film__content a.film-audio__link span')->each(function (Crawler $node){
            $audioInput = new AudioInput($node->text());
            $this->validator->validate($audioInput);
            return $audioInput;
        });
        return $filmAudio;
    }

    private function parseCast($crawlerChild){
        $castGenre = $crawlerChild->filter('div.film__actor a')->each(function (Crawler $node){
            $castInput = new CastInput($node->text(),$node->link()->getUri());
            $this->validator->validate($castInput);
            return $castInput;
        });
        return $castGenre;
    }

    private function parseCountry($crawlerChild){
        $filmCountry = $crawlerChild->filter('div.film__countries a.film-left__link')->each(function (Crawler $node){
            $countryInput = new CountryInput($node->text());
            $this->validator->validate($countryInput);
            return $countryInput;
        });
        return $filmCountry;
    }

    private function parseDirector($crawlerChild){
        $filmDirector = $crawlerChild->filter('div.film__directors');
        $directorName = $crawlerChild->filter('div.film__directors span')->text();
        $directorLink = $crawlerChild->filter('div.film__directors  a')->link()->getUri();
        $directorInput = new DirectorInput($directorName,$directorLink);
        $this->validator->validate($directorInput);
        return $filmDirector;
    }

    private function parseImage($crawlerChild){
        $imageLink=$crawlerChild->filter('div.film-right  div.film-right__img picture img')->image()->getUri();
            $imageInput = new ImageInput($imageLink);
            $this->validator->validate($imageInput);

        return $imageInput;
    }


    private function parseFilmBySweet(FilmInput $filmInput, $crawlerChild, string $lang = 'en')
    {
        $movieId = preg_replace("/[^0-9]/", '', $crawlerChild->filter('a.modal__lang-item')->link()->getUri());
        $title = $crawlerChild->filter('.container-fluid_padding li')->last()->text();
        $description = $crawlerChild->filter('p.film-descr__text')->text();
        $banner = $crawlerChild->filter('div.film-right  div.film-right__img picture img')->image()->getUri();
        $age =  $crawlerChild->filter('div.film__age div.film-left__details div.film-left__flex ')->text();
        $years=  $crawlerChild->filter('.film__years > .film-left__details')->text();
        $duration= $this->convertTime($crawlerChild->filter(' span.film-left__time')->text());
        $ratingNode = $crawlerChild->filter('.film__rating');

        if ($ratingNode->count() !== 0) {
            $rating = $ratingNode->filter('.film-left__details > span')->text();
            $filmInput->setRating($rating);
        }

        $filmFieldTranslation = new FilmFieldTranslationInput($title, $description, $banner, $lang);
        $this->validator->validate($filmFieldTranslation);
        $filmInput->addFilmFieldTranslationInput($filmFieldTranslation);
        $filmInput->setMovieId((int)$movieId);
        $filmInput->setAge((int)$age);
        $filmInput->setYears($years);
        $filmInput->setDuration((int)$duration);

        if ($lang === 'en') {
            $countryInput = $this->parseCountry($crawlerChild);
            $filmInput->addCountryInput($countryInput);

            $genreNode = $crawlerChild->filter('div.film__genres a');
            if ($genreNode->count() !== 0) {
                $genreInput = $this->parseGenre($crawlerChild);
                $filmInput->addGenreInput($genreInput);
            }

            $directorNode = $crawlerChild->filter('div.film__directors');
            if ($directorNode->count() !== 0) {
                $directorInput = $this->parseDirector($crawlerChild);
                $filmInput->addDirectorInput($directorInput);
            }

            $castNode = $crawlerChild->filter('div.film__actor a');
            if ($castNode->count() !== 0) {
                $castInput = $this->parseCast($crawlerChild);
                $filmInput->addCastInput($castInput);
            }

            $audioNode = $crawlerChild->filter('a.film-audio__link');
            if ($audioNode->count() !== 0) {
                $audioInput = $this->parseAudio($crawlerChild);
                $filmInput->addAudioInput($audioInput);
            }
        }
        sleep(rand(0,3));
        return $filmInput;
    }
}
