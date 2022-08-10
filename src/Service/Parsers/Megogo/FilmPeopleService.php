<?php

namespace App\Service\Parsers\Megogo;

use App\DTO\PeopleInput;
use App\Interface\Parsers\FilmPeopleInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class SweetTvService
 */
class FilmPeopleService implements FilmPeopleInterface
{
    public function __construct(
        ValidatorInterface $validator,
    ) {
        $this->validator = $validator;
    }

    public function parseDirector($crawler): ?ArrayCollection
    {
        $directors = [];
        $directorName = $crawler->filter('a[itemprop="director"] div')->text();
        $data = $crawler->filter('a[itemprop="director"]')->attr('href');
        $directorLink = 'https://megogo.net' . $data;
        $directorInput = new PeopleInput($directorName, $directorLink);
        $this->validator->validate($directorInput);
        $directors[] = $directorInput;
        return new ArrayCollection($directors);
    }

    public function parseCast($crawler, $filmInput = null): ?ArrayCollection
    {
        $castGenre = $crawler->filter('div.video-persons .type-main a.link-default')->each(function (Crawler $node) {
            $link = 'https://megogo.net' . $node->attr('href');
            $name = $node->filter('div.video-person-name')->text();
            $castInput = new PeopleInput($name, $link);
            $this->validator->validate($castInput);
            return $castInput;
        });

        return new ArrayCollection($castGenre);
    }
}
