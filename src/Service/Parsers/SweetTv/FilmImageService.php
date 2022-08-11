<?php

namespace App\Service\Parsers\SweetTv;

use App\DTO\ImageInput;
use App\Interface\Parsers\FilmImageInterface;
use App\Utility\CrawlerTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class FilmImageService
 */
class FilmImageService implements FilmImageInterface
{
    use CrawlerTrait;

    private ValidatorInterface $validator;

    public function __construct(
        ValidatorInterface $validator,
    ) {
        $this->validator = $validator;
    }

    /**
     * @param $crawler
     * @return ArrayCollection
     */
    public function parseImage($crawler): ArrayCollection
    {
        $imageLink = $crawler->filter('.movie__item-img > img.img_wauto_hauto')->image()->getUri();
        $image = $this->getImageInput($imageLink);
        return new ArrayCollection([$image]);
    }

    public function getImageInput($link): ImageInput
    {
        $imageInput = new ImageInput($link);
        $this->validator->validate($imageInput);
        return $imageInput;
    }
}
