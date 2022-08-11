<?php


namespace App\Interface\Parsers;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class SweetTvService
 */
interface FilmPeopleInterface
{
    public function parseDirector($crawler): ?ArrayCollection;
    public function parseCast($crawler): ?ArrayCollection;
}

