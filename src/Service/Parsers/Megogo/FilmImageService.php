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
    use CrawlerTrait {
        CrawlerTrait::__construct as private __tConstruct;
    }

    public const LANG_DEFAULT = 'en';

    private ValidatorInterface $validator;

    public function __construct(
        ValidatorInterface $validator,
    ) {
        $this->__tConstruct();
        $this->validator = $validator;
    }

    public function getImageInput(string $link): ImageInput
    {
        $imageInput = new ImageInput($link);
        $this->validator->validate($imageInput);
        return $imageInput;
    }

    /**
     * @param Crawler $node
     * @return ArrayCollection
     * @throws GuzzleException
     */
    public function parseImage(Crawler $node): ArrayCollection
    {
        $link = $this->getCrawler($this->getContentLink($node->link()->getUri()))
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
