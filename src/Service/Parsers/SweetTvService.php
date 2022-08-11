<?php

declare(strict_types=1);

namespace App\Service\Parsers;

use App\DTO\FilmInput;
use App\DTO\AudioInput;
use App\DTO\CountryInput;
use App\DTO\PeopleInput;
use App\DTO\GenreInput;
use App\DTO\ImageInput;
use App\Entity\Provider;
use App\Repository\ProviderRepository;
use App\Repository\FilmByProviderRepository;
use App\Service\FilmByProviderService;
use App\Service\TaskService;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class SweetTvService
 */
class SweetTvService extends MainParserService
{
    public const LANG_DEFAULT = 'en';
    public string $parserName = Provider::SWEET_TV;
    public string $defaultLink = 'https://sweet.tv/en/movies/all-movies/sort=5';

    /**
     * @param ProviderRepository $providerRepository
     */
    public function __construct(
        TaskService $taskService,
        ValidatorInterface $validator,
        FilmByProviderRepository $filmByProviderRepository,
        ProviderRepository $providerRepository,
        FilmByProviderService $filmByProviderService
    ) {
        parent::__construct($taskService, $validator, $providerRepository,$filmByProviderRepository,$filmByProviderService);
    }

    /**
     * @return string
     */
    public function getParserName(): string
    {
        return $this->parserName;
    }

    /**
     * @return string
     */
    public function getDefaultLink(): string
    {
        return $this->defaultLink;
    }

    /**
     * @param $linkByFilms
     * @return void
     * @throws GuzzleException
     * @throws Exception
     */
    protected function parserPages($linkByFilms): void
    {
        $html = $this->getContentLink($linkByFilms);
        $crawler = $this->getCrawler($html);
        $pageMax = (int)$crawler->filter('.pagination li')->last()->text();
        $page = 1;
        $this->taskService->setWorkStatus($this->task);
        while ($page <= $pageMax) {
            try {
                $this->parseFilmsByPage($linkByFilms . '/page/$page', $page);
            } catch (Exception $e) {
                $this->taskService->setErrorStatus($this->task, $e->getMessage());
                throw new Exception($e->getMessage());
            }
            $page++;
        }
    }

    /**
     * @param string $linkByFilms
     * @param int $page
     * @return void
     * @throws GuzzleException
     * @throws Exception
     */
    protected function parseFilmsByPage(string $linkByFilms, int $page): void
    {
        $crawler = $this->getPageCrawler($linkByFilms, $page);
        $crawler->filter('.movie__item-link')->each(function ($node) {
            $this->addFilmInput($node);
        });
    }

    /**
     * @param $linkByFilms
     * @return Crawler
     */
    protected function getPageCrawler($linkByFilms, $page): Crawler
    {
        $link = str_replace('$page', (string)$page, $linkByFilms);
        $html = $this->getContentLink($link);
        return $this->getCrawler($html);
    }

    /**
     * @param string $linkFilm
     * @return string
     */
    protected function parseFilmId($linkFilm): string
    {
        $re = '/https:\/\/sweet.tv\/en\/movie\/([0-9]*)-(.*)/';
        preg_match($re, $linkFilm, $matches, PREG_OFFSET_CAPTURE, 0);
        return $matches[1][0];
    }

    /**
     * @param $crawler
     * @return ArrayCollection
     */
    protected function parseImage($crawler): ArrayCollection
    {
        $imageLink = $crawler->filter('.movie__item-img > img.img_wauto_hauto')->image()->getUri();
        $image = $this->getImageInput($imageLink);
        return new ArrayCollection([$image]);
    }

    private function getImageInput(string $link): ImageInput
    {
        $imageInput = new ImageInput($link);
        $this->validator->validate($imageInput);
        return $imageInput;
    }
}
