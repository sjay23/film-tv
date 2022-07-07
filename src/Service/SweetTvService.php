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
use App\Entity\Provider;
use App\Repository\ProviderRepository;
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
    public const LANG_DEFAULT = 'en';

    public const LANGS = [
        'en',
        'ru',
        'uk'
    ];

    /**
     * @var Client
     */
    private Client $client;
    
    /**
     * @var ProviderRepository
     */
    private ProviderRepository $providerRepository;

    /**
     * @var ValidatorInterface
     */
    private ValidatorInterface $validator;

    /**
     * @param ValidatorInterface $validator
     * @param ProviderRepository $providerRepository
     */
    public function __construct(
        ValidatorInterface $validator,
        ProviderRepository $providerRepository
    )
    {
        $this->validator = $validator;
        $this->providerRepository = $providerRepository;
        $this->client = new Client();
    }

    /**
     * @return void
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
            $filmsData = $this->parseFilmsByPage($linkByFilms . '/page/$page', $page);
            dump($filmsData);
            die();
        }
    }

    /**
     * @param string $link
     * @param int $page
     * @return array
     * @throws GuzzleException
     */
    private function parseFilmsByPage(string $link, int $page): array
    {
        $link = str_replace('$page', (string)$page, $link);
        $html = $this->getContentLink($link);
        $crawler = $this->getCrawler($html);
        $filmsData = $crawler->filter('.movie__item-link')->each(function ($node) {
            $filmInput = new FilmInput();
            $linkFilm = $node->link()->getUri();
            $filmInput->setLink($linkFilm);
            foreach (self::LANGS as $lang) {
                $htmlChild = $this->getContentLink($linkFilm, $lang);
                $crawlerChild = $this->getCrawler($htmlChild);
                $filmInput = $this->parseFilmBySweet($filmInput, $crawlerChild, $lang);
            }
            $posterInput = $this->parseImage($node);
            $filmInput->addImageInput($posterInput);
            $provider = $this->getProvider();
            $filmInput->setProvider($provider);
            $this->validator->validate($filmInput);
            dump($filmInput);die();
            return $filmInput;
        });
        return $filmsData;
    }

    /**
     * @param FilmInput $filmInput
     * @param $crawlerChild
     * @param string $lang
     * @return FilmInput
     */
    private function parseFilmBySweet(FilmInput $filmInput, $crawlerChild, string $lang = self::LANG_DEFAULT): FilmInput
    {
        $filmFieldTranslation = $this->getFilmFieldTranslation($crawlerChild,$lang);
        $filmInput->addFilmFieldTranslationInput($filmFieldTranslation);

        if ($lang === self::LANG_DEFAULT) {
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

            $countriesCollect = $this->parseCountry($crawlerChild);
            $filmInput->setCountriesInput($countriesCollect);
            $genreCollect = $this->parseGenre($crawlerChild);
            $filmInput->setGenresInput($genreCollect);
            $directorCollect = $this->parseDirector($crawlerChild);
            $filmInput->setDirectorsInput($directorCollect);
            $castCollect = $this->parseCast($crawlerChild);
            $filmInput->setCastsInput($castCollect);
            $audioCollect = $this->parseAudio($crawlerChild);
            $filmInput->setAudiosInput($audioCollect);
        }

        sleep(rand(0,3));
        return $filmInput;
    }

    /**
     * @param string $str
     * @return int
     */
    private function convertTime(string $str): int
    {
        $a = preg_replace("/[^0-9]/", '', $str);
        $time = ((substr($a,0,2))*60)+((substr($a,-2,2)));
        return $time;
    }

    /**
     * @param string $html
     * @return Crawler
     */
    private function getCrawler(string $html): Crawler
    {
        return new Crawler($html);
    }

    /**
     * @return Provider|null
     */
    private function getProvider(): ?Provider
    {
        return $this->providerRepository->findOneBy(['name' => Provider::SWEET_TV]);
    }

    /**
     * @param string $link
     * @param string $lang
     * @return string|null
     * @throws GuzzleException
     */
    private function getContentLink(string $link, string $lang = self::LANG_DEFAULT): ?string
    {
        sleep(rand(0,3));
        if ($lang !== self::LANG_DEFAULT) {
            $link = str_replace(self::LANG_DEFAULT, $lang, $link);
        }
        echo "Parse link: " . $link . "\n";
        $response = $this->client->get($link);

        return (string) $response->getBody();
    }

    /**
     * @param $crawler
     * @return ArrayCollection
     */
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

    /**
     * @param $crawler
     * @return ArrayCollection
     */
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

    /**
     * @param $crawler
     * @return ArrayCollection
     */
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

    /**
     * @param $crawler
     * @return ArrayCollection
     */
    private function parseCountry($crawler): ArrayCollection
    {
        $filmCountry = $crawler->filter('div.film__countries a.film-left__link')->each(function (Crawler $node){
            $countriesInput = new CountryInput($node->text());
            $this->validator->validate($countriesInput);
            return $countriesInput;
        });
        return new ArrayCollection($filmCountry);
    }

    /**
     * @param $crawler
     * @return ArrayCollection
     */
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

    /**
     * @param $crawler
     * @return ImageInput
     */
    private function parseImage($crawler): ImageInput
    {
        $imageLink = $crawler->filter('.movie__item-img > img.img_wauto_hauto')->image()->getUri();
        $imageInput = new ImageInput($imageLink);
        $this->validator->validate($imageInput);

        return $imageInput;
    }

    /**
     * @param $crawler
     * @return string|null
     */
    private function parseRating($crawler): ?string
    {
        $rating = null;
        $node = $crawler->filter('.film__rating');
        if ($node->count() !== 0) {
            $rating = $node->filter('.film-left__details > span')->text();
        }

        return $rating;
    }

    /**
     * @param $crawlerChild
     * @param $lang
     * @return FilmFieldTranslationInput
     */
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
