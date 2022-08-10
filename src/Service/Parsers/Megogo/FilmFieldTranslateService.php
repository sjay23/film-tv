<?php


namespace App\Service\Parsers\Megogo;

use App\DTO\ImageInput;
use App\Interface\Parsers\FilmFieldTranslateInterface;

/**
 * Class SweetTvService
 */
class FilmFieldTranslateService implements FilmFieldTranslateInterface
{

    public function parseBannerTranslate($crawlerChild): ImageInput
    {
        $bannerLink = $crawlerChild->filter('div.thumbnail div.thumb img')->image()->getUri();
        return $this->getImageInput($bannerLink);
    }

    public function parseDescriptionTranslate($crawlerChild): ?string
    {
        return $crawlerChild->filter('div.video-description')->text();
    }

    public function parseTitleTranslate($crawlerChild): string
    {
        $data = $crawlerChild->filterXpath("//meta[@property='og:title']")->extract(['content']);
        $title = $data[0];
        return $title;
    }

}
