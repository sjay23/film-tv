<?php

namespace App\Service\Parsers\Megogo;

use App\DTO\AudioInput;
use App\DTO\CountryInput;
use App\Interface\Parsers\FilmFieldInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FilmFieldService implements FilmFieldInterface
{
    private ValidatorInterface $validator;

    public function __construct(
        ValidatorInterface $validator,
    ) {
        $this->validator = $validator;
    }

    /**
     * @param string $linkFilm
     * @return string
     */
    public function parseFilmId(string $linkFilm): string
    {

        $re = '/https:\/\/megogo.net\/en\/view\/([0-9]*)-(.*)/';
        preg_match($re, $linkFilm, $matches, PREG_OFFSET_CAPTURE, 0);
        return $matches[1][0];
    }

    public function parseLink(Crawler $crawler): ?string
    {
        return $crawler->link()->getUri();
    }

    public function parseAge(Crawler $crawler): ?string
    {
        return $crawler->filter('.videoInfoPanel-age-limit')->text();
    }

    public function parseRating(Crawler $crawler): ?float
    {
        $rating = null;
        $node = $crawler->filter('.videoInfoPanel-rating');
        if ($node->count() !== 0) {
            $rating = (float)$node->filter('span.value')->text();
        }

        return  $rating;
    }

    public function parseYear(Crawler $crawlerChild): ?string
    {
        return $crawlerChild->filter('span.video-year')->text();
    }

    public function parseDuration(Crawler $crawlerChild): ?int
    {
        $duration = (int)(preg_replace(
            "/[^,.0-9]/",
            '',
            $crawlerChild->filter(' div.video-duration span')->text()
        ));
        return $duration;
    }

    public function parseCountry(Crawler $crawler): ?ArrayCollection
    {
        $data = $crawler->filterXPath("//meta[@property='ya:ovs:country']")->extract(['content']);
        $countries = explode(',', $data[0]);
        $filmCountry = [];
        foreach ($countries as $country) {
            $countriesInput = new CountryInput($country);
            $this->validator->validate($countriesInput);
            $filmCountry[] = $countriesInput;
        }
        return new ArrayCollection($filmCountry);
    }

    public function parseGenre(Crawler $crawler): ?ArrayCollection
    {
        $data = $crawler->filterXPath("//meta[@property='ya:ovs:genre']")->extract(['content']);
        $genres = explode(',', $data[0]);
        $filmGenre = [];
        foreach ($genres as $genre) {
            $genresInput = new CountryInput($genre);
            $this->validator->validate($genresInput);
            $filmGenre[] = $genresInput;
        }
        return new ArrayCollection($filmGenre);
    }

    public function parseAudio(Crawler $crawler): ?ArrayCollection
    {
        $data = $crawler->filterXPath("//meta[@property='ya:ovs:languages']")->extract(['content']);
        $audios = explode(',', $data[0]);
        $filmAudio = [];
        foreach ($audios as $audio) {
            $audioInput = new AudioInput($audio);
            $this->validator->validate($audioInput);
            $filmAudio[] = $audioInput;
        }
        return new ArrayCollection($filmAudio);
    }



}
