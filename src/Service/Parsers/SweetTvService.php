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
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class SweetTvService
 */
class SweetTvService extends MainParserService
{
    public const LANG_DEFAULT = 'en';

    /**
     * @var FilmByProviderRepository
     */
    private FilmByProviderRepository $filmByProviderRepository;

    /**
     * @var FilmByProviderService
     */
    private FilmByProviderService $filmByProviderService;

    public string $parserName = Provider::SWEET_TV;
    public string $defaultLink = 'https://sweet.tv/en/movies/all-movies/sort=5';

    /**
     * @param FilmByProviderRepository $filmByProviderRepository
     * @param FilmByProviderService $filmByProviderService
     * @param ProviderRepository $providerRepository
     */
    public function __construct(
        TaskService $taskService,
        ValidatorInterface $validator,
        FilmByProviderRepository $filmByProviderRepository,
        FilmByProviderService $filmByProviderService,
        ProviderRepository $providerRepository
    ) {
        parent::__construct($taskService, $validator, $providerRepository);
        $this->filmByProviderService = $filmByProviderService;
        $this->filmByProviderRepository = $filmByProviderRepository;
        $this->client = new Client();
    }

    /**
     * @return void
     * @throws Exception
     */
    public function runExec(): void
    {
        $this->exec($this->defaultLink, $this->parserName);
    }

    /**
     * @param $linkByFilms
     * @return void
     * @throws GuzzleException
     */
    public function parserPages($linkByFilms): void
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
     * @param string $link
     * @param int $page
     * @return void
     * @throws GuzzleException
     * @throws Exception
     */
    protected function parseFilmsByPage(string $link, int $page): void
    {
        $link = str_replace('$page', (string)$page, $link);
        $html = $this->getContentLink($link);
        $crawler = $this->getCrawler($html);
            $crawler->filter('.movie__item-link')->each(function ($node) {
                if ($this->task->getStatus() == 0) {
                    throw new Exception('Task is stop manual.');
                }
                $filmInput = new FilmInput();
                $linkFilm = $node->link()->getUri();
                $filmInput->setLink($linkFilm);
                $movieId = $this->parseFilmId($linkFilm);
                $filmInput->setMovieId((int)$movieId);
                $posterInput = $this->parseImage($node);
                $filmInput->addImageInput($posterInput);
                $provider = $this->getProvider(Provider::SWEET_TV);
                $filmInput->setProvider($provider);
                $film = $this->filmByProviderRepository->findOneBy(['movieId' => $movieId]);
                if (!$film) {
                    foreach (self::LANGS as $lang) {
                        $htmlChild = $this->getContentLink($linkFilm, $lang);
                        $crawlerChild = $this->getCrawler($htmlChild);
                        if ($crawlerChild->filter('h1')->text() == 'Movies') {
                            // Пропускаем фильмы с редиректом на главную
                            return;
                        }
                        $filmInput = $this->parseFilmByProvider($filmInput, $crawlerChild, $lang);
                    }
                    $this->validator->validate($filmInput);
                    $film = $this->filmByProviderService->addFilmByProvider($filmInput);
                }
                $this->taskService->updateTask($film, $this->task);
            });
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
    protected function parseGenre($crawler): ArrayCollection
    {
        $node = $crawler->filter('div.film__genres a');
        $filmGenre = [];
        if ($node->count() !== 0) {
            $filmGenre = $crawler->filter('div.film__genres a')->each(function (Crawler $node) {
                $genreInput = new GenreInput($node->text());
                $this->validator->validate($genreInput);
                return $genreInput;
            });
        }
        return new ArrayCollection($filmGenre);
    }

    /**
     * @param $crawler
     * @return ArrayCollection
     */
    protected function parseAudio($crawler): ArrayCollection
    {
        $node = $crawler->filter('a.film-audio__link');
        $filmAudio = [];
        if ($node->count() !== 0) {
            $filmAudio = $crawler->filter('div.film__sounds div.film__content a.film-audio__link span')
                ->each(function (Crawler $node) {
                    $audioInput = new AudioInput(rtrim($node->text(), ','));
                    $this->validator->validate($audioInput);
                    return $audioInput;
                });
        }
        return new ArrayCollection(array_unique($filmAudio, SORT_REGULAR));
    }

    /**
     * @param $crawler
     * @return ArrayCollection
     */
    protected function parseCast($crawler): ArrayCollection
    {
        $node = $crawler->filter('div.film__actor a');
        $castGenre = [];
        if ($node->count() !== 0) {
            $castGenre = $crawler->filter('div.film__actor a')->each(function (Crawler $node) {
                $castInput = new PeopleInput($node->text(), $node->link()->getUri());
                $this->validator->validate($castInput);
                return $castInput;
            });
        }
        return new ArrayCollection($castGenre);
    }

    /**
     * @param $crawler
     * @return ArrayCollection
     */
    protected function parseCountry($crawler): ArrayCollection
    {
        $filmCountry = $crawler->filter('div.film__countries a.film-left__link')->each(function (Crawler $node) {
            $countriesInput = new CountryInput($node->text());
            $this->validator->validate($countriesInput);
            return $countriesInput;
        });
        return new ArrayCollection($filmCountry);
    }

    /**
     * @param $crawler
     * @return ArrayCollection
     */
    protected function parseDirector($crawler): ArrayCollection
    {
        $node = $crawler->filter('div.film__directors');
        $directors = [];
        if ($node->count() !== 0) {
            $directorName = $crawler->filter('div.film__directors span')->text();
            $directorLink = $crawler->filter('div.film__directors  a')->link()->getUri();
            $directorInput = new PeopleInput($directorName, $directorLink);
            $this->validator->validate($directorInput);
            $directors[] = $directorInput;
        }
        return new ArrayCollection($directors);
    }

    /**
     * @param $crawler
     * @return ImageInput
     */
    protected function parseImage($crawler): ImageInput
    {
        $imageLink = $crawler->filter('.movie__item-img > img.img_wauto_hauto')->image()->getUri();

        return $this->getImageInput($imageLink);
    }

    private function getImageInput(string $link): ImageInput
    {
        $imageInput = new ImageInput($link);
        $this->validator->validate($imageInput);
        return $imageInput;
    }

    /**
     * @param $crawler
     * @return string|null
     */
    protected function parseRating($crawler): ?string
    {
        $rating = null;
        $node = $crawler->filter('.film__rating');
        if ($node->count() !== 0) {
            $rating = $node->filter('.film-left__details > span')->text();
        }

        return $rating;
    }

    /**
     * @param $crawler
     * @return string|null
     */
    protected function parseAge($crawler): ?string
    {
        $age = null;
        $node = $crawler->filter('.film__age');
        if ($node->count() !== 0) {
            $age = $node->filter('.film-left__details div.film-left__flex ')->text();
        }

        return $age;
    }

    /**
     * @param $crawlerChild
     * @return string|null
     */
    protected function parseYear($crawlerChild): ?string
    {
        return $crawlerChild->filter('.film__years > .film-left__details')->text();
    }

    /**
     * @param $crawlerChild
     * @return int
     */
    protected function parseDuration($crawlerChild): ?int
    {
        return  $this->convertTime($crawlerChild->filter(' span.film-left__time')->text());
    }

    /**
     * @param $crawlerChild
     * @return string|null
     */
    protected function parseTitleTranslate($crawlerChild): ?string
    {
        return $crawlerChild->filter('.container-fluid_padding li')->last()->text();
    }

    /**
     * @param $crawlerChild
     * @return string|null
     */
    protected function parseDescriptionTranslate($crawlerChild): ?string
    {
        $node = $crawlerChild->filter('div.film-descr p');
        if ($node->count() !== 0) {
            return $node->text();
        }
        return null;
    }

    /**
     * @param $crawlerChild
     * @return ImageInput|null
     */
    protected function parseBannerTranslate($crawlerChild): ?ImageInput
    {
        $bannerNode = $crawlerChild->filter('div.film-right  div.film-right__img source');
        if ($bannerNode->count() > 0) {
            $bannerLink = $bannerNode->attr('srcset');
            return $this->getImageInput($bannerLink);
        }
        return null;
    }
}
