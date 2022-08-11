<?php


namespace App\Service\Parsers\SweetTv;

use App\DTO\ImageInput;
use App\Interface\Parsers\FilmFieldTranslateInterface;
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

    public function parseBannerTranslate($crawlerChild): ?ImageInput
    {
        $bannerNode = $crawlerChild->filter('div.film-right  div.film-right__img source');
        if ($bannerNode->count() > 0) {
            $bannerLink = $bannerNode->attr('srcset');
            return $this->getImageInput($bannerLink);
        }
        return null;
    }

    public function parseDescriptionTranslate($crawlerChild): ?string
    {
        $node = $crawlerChild->filter('div.film-descr p');
        if ($node->count() !== 0) {
            return $node->text();
        }
        return null;
    }

    public function parseTitleTranslate($crawlerChild): string
    {
        return $crawlerChild->filter('.container-fluid_padding li')->last()->text();
    }

}
