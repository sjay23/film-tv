<?php

namespace App\Service\Parsers\SweetTv;

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

    public function parseCast($crawler): ?ArrayCollection
    {
        $node = $crawler->filter('div.film__actor a');
        $castGenre = [];
        if ($node->count() !== 0) {
            $castGenre = $crawler->filter('div.film__actor a')->each(function (Crawler $node) {
                $castInput = new PeopleInput($node->text(), $node->link()->getUri());
                $this->validator->validate($castInput);
                return $castInput;
            });
        }
        return new ArrayCollection($castGenre);
    }
}
