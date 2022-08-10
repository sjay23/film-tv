<?php


namespace App\Service\Parsers\Megogo;

use App\DTO\AudioInput;
use App\DTO\CountryInput;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class SweetTvService
 */
class FilmFieldService implements FilmField
{

    public function __construct(
        ValidatorInterface $validator,
    )
    {
        $this->validator = $validator;
    }

    public function parseAge($crawler): ?string
    {
        return $crawler->filter('.videoInfoPanel-age-limit')->text();
    }

    public function parseRating($crawler): ?string
    {
        $rating = null;
        $node = $crawler->filter('.videoInfoPanel-rating');
        if ($node->count() !== 0) {
            $rating = $node->filter('span.value')->text();
        }

        return $rating;
    }

    public function parseYear($crawlerChild): ?string
    {
        return $crawlerChild->filter('span.video-year')->text();
    }

    public function parseDuration($crawlerChild): ?int
    {
        $duration = (int)(preg_replace(
            "/[^,.0-9]/",
            '',
            $crawlerChild->filter(' div.video-duration span')->text()
        ));
        return $duration;
    }

    public function parseCountry($crawler): ?ArrayCollection
    {
        $data = $crawler->filterXpath("//meta[@property='ya:ovs:country']")->extract(['content']);
        $countries = explode(',', $data[0]);
        $filmCountry = [];
        foreach ($countries as $country) {
            $countriesInput = new CountryInput($country);
            $this->validator->validate($countriesInput);
            $filmCountry[] = $countriesInput;
        }
        return new ArrayCollection($filmCountry);
    }

    public function parseGenre($crawler): ?ArrayCollection
    {
        $data = $crawler->filterXpath("//meta[@property='ya:ovs:genre']")->extract(['content']);
        $genres = explode(',', $data[0]);
        $filmGenre = [];
        foreach ($genres as $genre) {
            $genresInput = new CountryInput($genre);
            $this->validator->validate($genresInput);
            $filmGenre[] = $genresInput;
        }
        return new ArrayCollection($filmGenre);
    }

    public function parseAudio($crawler): ?ArrayCollection
    {
        $data = $crawler->filterXpath("//meta[@property='ya:ovs:languages']")->extract(['content']);
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
