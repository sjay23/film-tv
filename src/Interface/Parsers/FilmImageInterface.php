<?php


namespace App\Interface\Parsers;

use App\DTO\ImageInput;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class SweetTvService
 */
interface FilmImageInterface
{
    public function parseImage($linkFilm): ?ArrayCollection;
    public function getImageInput($link): ?ImageInput;
}

