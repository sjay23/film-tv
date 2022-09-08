<?php

namespace App\Service\Parsers\Megogo;

use App\DTO\PeopleInput;
use App\Interface\Parsers\FilmPeopleInterface;
use App\Utility\CrawlerTrait;
use Doctrine\Common\Collections\ArrayCollection;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FilmPeopleService implements FilmPeopleInterface
{
    use CrawlerTrait {
        CrawlerTrait::__construct as private __tConstruct;
    }

    public const LANG_DEFAULT = 'en';

    private ValidatorInterface $validator;

    public function __construct(
        ValidatorInterface $validator,
    ) {
        $this->__tConstruct();
        $this->validator = $validator;
    }

    /**
     * @throws GuzzleException
     */
    public function parseDirector(Crawler $crawler): ?ArrayCollection
    {
        $link = $crawler
            ->filter('ul.video-view-tabs')
            ->children('.nav-item')
            ->eq(1)
            ->children('a')
            ->attr('href');

        $html = $this->getContentLink('https://megogo.net' . $link);

        $crawler = $this->getCrawler($html);
        $directors = [];
        $directorName = $crawler->filter('a[itemprop="director"] div')->text();
        $data = $crawler->filter('a[itemprop="director"]')->attr('href');
        $directorLink = 'https://megogo.net' . $data;
        $directorInput = new PeopleInput($directorName, $directorLink);
        $this->validator->validate($directorInput);
        $directors[] = $directorInput;
        return new ArrayCollection($directors);
    }

    public function parseCast(Crawler $crawler): ?ArrayCollection
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
