<?php


namespace App\Interface\Parsers;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class SweetTvService
 */
interface FilmFieldInterface
{
    public function parseAge($crawler): ?string;
    public function parseFilmId($linkFilm): ?string;
    public function parseRating($crawler): ?string;
    public function parseYear($crawlerChild): ?string;
    public function parseDuration($crawlerChild): ?int;
    public function parseCountry($crawler): ?ArrayCollection;
    public function parseGenre($crawler): ?ArrayCollection;
    public function parseAudio($crawler): ?ArrayCollection;

}
