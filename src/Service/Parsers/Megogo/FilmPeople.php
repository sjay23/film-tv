<?php


namespace App\Service\Parsers\Megogo;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class SweetTvService
 */
interface FilmPeople
{
    public function parseDirector($crawler): ?ArrayCollection;
    public function parseCast($crawler, $filmInput): ?ArrayCollection;
}

