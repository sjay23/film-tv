<?php


namespace App\Service\Parsers\Megogo;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class SweetTvService
 */
interface FilmField
{
    public function parseAge($crawler): ?string;
    public function parseRating($crawler): ?string;
    public function parseYear($crawlerChild): ?string;
    public function parseDuration($crawlerChild): ?int;
    public function parseCountry($crawler): ?ArrayCollection;
    public function parseGenre($crawler): ?ArrayCollection;
    public function parseAudio($crawler): ?ArrayCollection;

}
