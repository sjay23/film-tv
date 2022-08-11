<?php


namespace App\Interface\Parsers;

use App\DTO\ImageInput;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DomCrawler\Crawler;

interface FilmImageInterface
{
    public function parseImage(Crawler $node): ?ArrayCollection;
    public function getImageInput(string $link): ?ImageInput;
}

