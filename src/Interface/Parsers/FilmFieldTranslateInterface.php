<?php

namespace App\Interface\Parsers;

use App\DTO\ImageInput;

/**
 * Class SweetTvService
 */
interface FilmFieldTranslateInterface
{
    public function parseBannerTranslate($crawlerChild): ?ImageInput;
    public function parseDescriptionTranslate($crawlerChild): ?string;
    public function parseTitleTranslate($crawlerChild): ?string;
}
