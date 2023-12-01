<?php


namespace App\Interface\Parsers;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DomCrawler\Crawler;

interface FilmFieldInterface
{
    public function parseAge(Crawler $crawler): ?string;
    public function parseFilmId(string $linkFilm): ?string;
    public function parseLink(Crawler $crawler): ?string;
    public function parseRating(Crawler $crawler): ?float;
    public function parseYear(Crawler $crawlerChild): ?string;
    public function parseDuration(Crawler $crawlerChild): ?int;
    public function parseCountry(Crawler $crawler): ?ArrayCollection;
    public function parseGenre(Crawler $crawler): ?ArrayCollection;
    public function parseAudio(Crawler $crawler): ?ArrayCollection;
}
