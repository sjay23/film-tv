<?php

namespace App\Service\Parsers\Megogo;

use App\DTO\ImageInput;

/**
 * Class SweetTvService
 */
interface FilmFieldTranslate
{
    public function parseBannerTranslate($crawlerChild): ?ImageInput;
    public function parseDescriptionTranslate($crawlerChild): ?string;
    public function parseTitleTranslate($crawlerChild): ?string;
}
