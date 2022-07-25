<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\FilmFieldTranslationInput;
use App\DTO\FilmInput;
use App\DTO\AudioInput;
use App\DTO\CountryInput;
use App\DTO\PeopleInput;
use App\DTO\GenreInput;
use App\Command\Cron\CommandTaskUpload;
use App\DTO\ImageInput;
use App\Entity\CommandTask;
use App\Service\TaskService;
use App\Entity\Provider;
use App\Repository\ProviderRepository;
use App\Repository\CommandTaskRepository;
use App\Repository\FilmByProviderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use App\Service\ImageFileService;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class SweetTvService
 */
class SweetTvService
{
    public const LANG_DEFAULT = 'en';

    public const LANGS = [
        'en',
        'ru',
        'uk'
    ];

    /**
     * @var Client
     */
    private Client $client;

    /**
     * @var TaskService
     */
    private TaskService $taskService;

    /**
     * @var ProviderRepository
     */
    private ProviderRepository $providerRepository;

    /**
     * @var FilmByProviderRepository
     */
    private FilmByProviderRepository $filmByProviderRepository;

    /**
     * @var FilmByProviderService
     */
    private FilmByProviderService $filmByProviderService;

    /**
     * @var ValidatorInterface
     */
    private ValidatorInterface $validator;

    /**
     * @param TaskService $taskService
     * @param ValidatorInterface $validator
     * @param ProviderRepository $providerRepository
     * @param FilmByProviderRepository $filmByProviderRepository
     * @param FilmByProviderService $filmByProviderService
     * @param CommandTaskRepository $commandTaskRepository
     */
    public function __construct(
        TaskService $taskService,
        ValidatorInterface $validator,
        ProviderRepository $providerRepository,
        FilmByProviderRepository $filmByProviderRepository,
        FilmByProviderService $filmByProviderService,
        CommandTaskRepository $commandTaskRepository
    ) {
        $this->taskService = $taskService;
        $this->validator = $validator;
        $this->filmByProviderService = $filmByProviderService;
        $this->filmByProviderRepository = $filmByProviderRepository;
        $this->commandTaskRepository = $commandTaskRepository;
        $this->providerRepository = $providerRepository;
        $this->task = $this->taskService->getTask($this->getProvider());
        $this->client = new Client();
    }

    /**
     * @return void
     * @throws GuzzleException
     * @throws \Exception
     */
    public function exec()
    {
        $linkByFilms = 'https://sweet.tv/en/movies/all-movies/sort=5';
        $html = $this->getContentLink($linkByFilms);
        $crawler = $this->getCrawler($html);
        $pageMax = (int) $crawler->filter('.pagination li')->last()->text();
        $page = 2;
        $taskStatus = $this->task->getStatus();
        $this->taskService->updateCountTask($this->task);

        if ($taskStatus != 0) {
            throw new \Exception('Task is running or stop with error.');

        }
        while ($page <= $pageMax) {
            try {
                $this->parseFilmsByPage($linkByFilms . '/page/$page', $page);
                $this->taskService->setWorkStatus($this->task);
            } catch (\Exception $e) {
                $this->taskService->setErrorStatus($this->task);
                $this->taskService->setErrorDescription($this->task, $e->getMessage());
            }
        }
        $this->taskService->setNotWorkStatus($this->task);
    }

    /**
     * @param string $link
     * @param int $page
     * @return void
     * @throws GuzzleException
     */
    private function parseFilmsByPage(string $link, int $page): void
    {
        $link = str_replace('$page', (string)$page, $link);
        $html = $this->getContentLink($link);
        $crawler = $this->getCrawler($html);
        $crawler->filter('.movie__item-link')->each(function ($node) {
            $filmInput = new FilmInput();
            $linkFilm = $node->link()->getUri();
            $filmInput->setLink($linkFilm);
            $movieId = $this->getFilmId($linkFilm);
            $filmInput->setMovieId((int)$movieId);
            if (!$film = $this->filmByProviderRepository->findOneBy(['movieId' => $movieId])) {
                foreach (self::LANGS as $lang) {
                    $htmlChild = $this->getContentLink($linkFilm, $lang);
                    $crawlerChild = $this->getCrawler($htmlChild);
                    $filmInput = $this->parseFilmBySweet($filmInput, $crawlerChild, $lang);
                }
                $posterInput = $this->parseImage($node);
                $filmInput->addImageInput($posterInput);
                $provider = $this->getProvider();
                $filmInput->setProvider($provider);
                $this->validator->validate($filmInput);
                $film = $this->filmByProviderService->addFilmByProvider($filmInput);
                $this->filmByProviderService->uploadPoster($film);
                $this->filmByProviderService->uploadBanner($film);
                $this->taskService->updateTask($film, $this->task);
                $this->taskService->setNotWorkStatus($this->task);
            }
            return $film;
        });
    }

    /**
     * @param FilmInput $filmInput
     * @param $crawlerChild
     * @param string $lang
     * @return FilmInput
     */
    private function parseFilmBySweet(FilmInput $filmInput, $crawlerChild, string $lang = self::LANG_DEFAULT): FilmInput
    {
        $filmFieldTranslation = $this->getFilmFieldTranslation($crawlerChild, $lang);
        $filmInput->addFilmFieldTranslationInput($filmFieldTranslation);

        if ($lang === self::LANG_DEFAULT) {
            $age = $this->parseAge($crawlerChild);
            $years = $crawlerChild->filter('.film__years > .film-left__details')->text();
            $duration = $this->convertTime($crawlerChild->filter(' span.film-left__time')->text());
            $rating = $this->parseRating($crawlerChild);
            $filmInput->setAge($age);
            $filmInput->setRating((float)$rating);
            $filmInput->setYears((int)$years);
            $filmInput->setDuration((int)$duration);
            $countriesCollect = $this->parseCountry($crawlerChild);
            $filmInput->setCountriesInput($countriesCollect);
            $genreCollect = $this->parseGenre($crawlerChild);
            $filmInput->setGenresInput($genreCollect);
            $directorCollect = $this->parseDirector($crawlerChild);
            $filmInput->setDirectorsInput($directorCollect);
            $castCollect = $this->parseCast($crawlerChild);
            $filmInput->setCastsInput($castCollect);
            $audioCollect = $this->parseAudio($crawlerChild);
            $filmInput->setAudiosInput($audioCollect);
        }

        sleep(rand(0, 3));

        return $filmInput;
    }

    /**
     * @param string $str
     * @return int
     */
    private function convertTime(string $str): int
    {
        $a = preg_replace("/[^0-9]/", '', $str);
        $time = ((substr($a, 0, 2)) * 60) + ((substr($a, -2, 2)));
        return $time;
    }

    /**
     * @param string $linkFilm
     * @return string
     */
    private function getFilmId(string $linkFilm): string
    {
        $re = '/https:\/\/sweet.tv\/en\/movie\/([0-9]*)-(.*)/';
        preg_match($re, $linkFilm, $matches, PREG_OFFSET_CAPTURE, 0);
        return $matches[1][0];
    }

    /**
     * @param string $html
     * @return Crawler
     */
    private function getCrawler(string $html): Crawler
    {
        return new Crawler($html);
    }

    /**
     * @return Provider|null
     */
    private function getProvider(): ?Provider
    {
        return $this->providerRepository->findOneBy(['name' => Provider::SWEET_TV]);
    }

    /**
     * @param string $link
     * @param string $lang
     * @return string|null
     * @throws GuzzleException
     */
    private function getContentLink(string $link, string $lang = self::LANG_DEFAULT): ?string
    {
        sleep(rand(0, 3));
        if ($lang !== self::LANG_DEFAULT) {
            $link = str_replace(self::LANG_DEFAULT, $lang, $link);
        }
        echo 'Parse link: ' . $link . "\n";
        $response = $this->client->get($link);

        return (string) $response->getBody();
    }

    /**
     * @param $crawler
     * @return ArrayCollection
     */
    private function parseGenre($crawler): ArrayCollection
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
    private function parseAudio($crawler): ArrayCollection
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
    private function parseCast($crawler): ArrayCollection
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
    private function parseCountry($crawler): ArrayCollection
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
    private function parseDirector($crawler): ArrayCollection
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
    private function parseImage($crawler): ImageInput
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
    private function parseRating($crawler): ?string
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
    private function parseAge($crawler): ?string
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
     * @param $lang
     * @return FilmFieldTranslationInput
     */
    private function getFilmFieldTranslation($crawlerChild, $lang): FilmFieldTranslationInput
    {
        $title = $crawlerChild->filter('.container-fluid_padding li')->last()->text();
        $description = $crawlerChild->filter('div.film-descr p')->text();
        $bannerNode = $crawlerChild->filter('div.film-right  div.film-right__img source');
        if ($bannerNode->count() > 0) {
            $bannerLink = $bannerNode->attr('srcset');
            $imageInput = $this->getImageInput($bannerLink);
        }
        $filmFieldTranslation = new FilmFieldTranslationInput($title, $description, $lang);
        $filmFieldTranslation->setBannersInput($imageInput);
        $this->validator->validate($filmFieldTranslation);

        return $filmFieldTranslation;
    }
}
