<?php


namespace App\Interface\Parsers;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DomCrawler\Crawler;

interface FilmPeopleInterface
{
    public function parseDirector(Crawler $crawler): ?ArrayCollection;
    public function parseCast(Crawler $crawler): ?ArrayCollection;
}

