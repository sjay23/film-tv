<?php


namespace App\Interface\Parsers;

use App\DTO\ImageInput;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class SweetTvService
 */
interface FilmImageInterface
{
    public function parseImage($node): ?ArrayCollection;
    public function getImageInput($link): ?ImageInput;
}

