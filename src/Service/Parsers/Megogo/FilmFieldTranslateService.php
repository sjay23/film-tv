<?php


namespace App\Service\Parsers\Megogo;

use App\DTO\ImageInput;
use App\Interface\Parsers\FilmFieldTranslateInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FilmFieldTranslateService implements FilmFieldTranslateInterface
{

    private ValidatorInterface $validator;

    public function __construct(
        ValidatorInterface $validator,
    ) {
        $this->validator = $validator;
    }

    private function getImageInput(string $link): ImageInput
    {
        $imageInput = new ImageInput($link);
        $this->validator->validate($imageInput);
        return $imageInput;
    }

    public function parseBannerTranslate(Crawler $crawlerChild): ImageInput
    {
        $bannerLink = $crawlerChild->filter('div.thumbnail div.thumb img')->image()->getUri();
        return $this->getImageInput($bannerLink);
    }

    public function parseDescriptionTranslate(Crawler $crawlerChild): ?string
    {
        return $crawlerChild->filter('div.video-description')->text();
    }

    public function parseTitleTranslate(Crawler $crawlerChild): string
    {
        $data = $crawlerChild->filterXPath("//meta[@property='og:title']")->extract(['content']);
        $title = $data[0];
        return $title;
    }

}
