<?php

namespace App\Service\Parsers\Megogo;

use App\DTO\ImageInput;
use App\Interface\Parsers\FilmImageInterface;
use App\Utility\CrawlerTrait;
use Doctrine\Common\Collections\ArrayCollection;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class FilmImageService
 */
class FilmImageService implements FilmImageInterface
{
    use CrawlerTrait;
    public function __construct(
        ValidatorInterface $validator,
    ) {
        $this->validator = $validator;
    }

    public function getImageInput($link): ImageInput
    {
        $imageInput = new ImageInput($link);
        $this->validator->validate($imageInput);
        return $imageInput;
    }

    /**
     * @param $linkFilm
     * @return ArrayCollection
     * @throws GuzzleException
     */
    public function parseImage($linkFilm): ArrayCollection
    {
        $link = $this->getCrawler($this->getContentLink($linkFilm->link()->getUri()))
            ->filter('ul.video-view-tabs')
            ->children('.nav-item')
            ->eq(2)
            ->children('a')
            ->attr('href');
        $html = $this->getContentLink('https://megogo.net' . $link);
        $crawler = $this->getCrawler($html);
        $images = $crawler->filter('a.type-screenshot img.lazy_image')->each(function (Crawler $node) {
            $link =  $node->attr('data-original');
            return($this->getImageInput($link));
        });
        return new ArrayCollection($images);
    }
}
