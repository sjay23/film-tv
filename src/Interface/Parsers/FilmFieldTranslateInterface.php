<?php

namespace App\Interface\Parsers;

use App\DTO\ImageInput;
use Symfony\Component\DomCrawler\Crawler;

interface FilmFieldTranslateInterface
{
    public function parseBannerTranslate(Crawler $crawlerChild): ?ImageInput;
    public function parseDescriptionTranslate(Crawler $crawlerChild): ?string;
    public function parseTitleTranslate(Crawler $crawlerChild): ?string;
}
