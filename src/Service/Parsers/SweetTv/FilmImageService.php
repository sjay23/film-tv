<?php

namespace App\Service\Parsers\SweetTv;

use App\DTO\ImageInput;
use App\Interface\Parsers\FilmImageInterface;
use App\Utility\CrawlerTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DomCrawler\Crawler;
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
     * @param Crawler $node
     * @return ArrayCollection
     */
    public function parseImage(Crawler $node): ArrayCollection
    {
        $imageLink = $node->filter('.movie__item-img > img.img_wauto_hauto')->image()->getUri();
        $image = $this->getImageInput($imageLink);
        return new ArrayCollection([$image]);
    }

    public function getImageInput(string $link): ImageInput
    {
        $imageInput = new ImageInput($link);
        $this->validator->validate($imageInput);
        return $imageInput;
    }
}
