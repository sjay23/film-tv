<?php

namespace App\Service\Parsers\SweetTv;

use App\DTO\AudioInput;
use App\DTO\CountryInput;
use App\DTO\GenreInput;
use App\Interface\Parsers\FilmFieldInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class SweetTvService
 */
class FilmFieldService implements FilmFieldInterface
{
    public function __construct(
        ValidatorInterface $validator,
    ) {
        $this->validator = $validator;
    }

    /**
     * @param string $linkFilm
     * @return string
     */
    public function parseFilmId($linkFilm): string
    {
        $re = '/https:\/\/sweet.tv\/en\/movie\/([0-9]*)-(.*)/';
        preg_match($re, $linkFilm, $matches, PREG_OFFSET_CAPTURE, 0);
        return $matches[1][0];
    }


    public function parseAge($crawler): ?string
    {
        $age = null;
        $node = $crawler->filter('.film__age');
        if ($node->count() !== 0) {
            $age = $node->filter('.film-left__details div.film-left__flex ')->text();
        }

        return $age;
    }

    public function parseRating($crawler): ?string
    {
        $rating = null;
        $node = $crawler->filter('.film__rating');
        if ($node->count() !== 0) {
            $rating = $node->filter('.film-left__details > span')->text();
        }

        return $rating;
    }

    public function parseYear($crawlerChild): ?string
    {
        return $crawlerChild->filter('.film__years > .film-left__details')->text();
    }

    public function parseDuration($crawlerChild): ?int
    {
        $str = $crawlerChild->filter(' span.film-left__time')->text();
        $a = preg_replace("/[^0-9]/", '', $str);
        $time = ((substr($a, 0, 2)) * 60) + (substr($a, -2, 2));
        return  $time;
    }

    public function parseCountry($crawler): ?ArrayCollection
    {
        $filmCountry = $crawler->filter('div.film__countries a.film-left__link')->each(function (Crawler $node) {
            $countriesInput = new CountryInput($node->text());
            $this->validator->validate($countriesInput);
            return $countriesInput;
        });
        return new ArrayCollection($filmCountry);
    }

    public function parseGenre($crawler): ?ArrayCollection
    {
        $node = $crawler->filter('div.film__genres a');
        $filmGenre = [];
        if ($node->count() !== 0) {
            $filmGenre = $crawler->filter('div.film__genres a')->each(function (Crawler $node) {
                $genreInput = new GenreInput($node->text());
                $this->validator->validate($genreInput);
                return $genreInput;
            });
        }
        return new ArrayCollection($filmGenre);
    }

    public function parseAudio($crawler): ?ArrayCollection
    {
        $node = $crawler->filter('a.film-audio__link');
        $filmAudio = [];
        if ($node->count() !== 0) {
            $filmAudio = $crawler->filter('div.film__sounds div.film__content a.film-audio__link span')
                ->each(function (Crawler $node) {
                    $audioInput = new AudioInput(rtrim($node->text(), ','));
                    $this->validator->validate($audioInput);
                    return $audioInput;
                });
        }
        return new ArrayCollection(array_unique($filmAudio, SORT_REGULAR));
    }
}
