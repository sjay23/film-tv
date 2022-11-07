<?php

namespace App\Service\Parsers;

use App\DTO\FilmFieldTranslationInput;
use App\DTO\FilmInput;
use App\Entity\CommandTask;
use App\Entity\Provider;
use App\Interface\Parsers\FilmFieldInterface;
use App\Interface\Parsers\FilmFieldTranslateInterface;
use App\Interface\Parsers\FilmImageInterface;
use App\Interface\Parsers\FilmPeopleInterface;
use App\Repository\FilmByProviderRepository;
use App\Repository\ProviderRepository;
use App\Service\FilmByProviderService;
use App\Service\TaskService;
use App\Utility\CrawlerTrait;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\GuzzleException;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class MainParserService
{
    use CrawlerTrait {
        CrawlerTrait::__construct as private __tConstruct;
    }

    protected const LANG_DEFAULT = 'en';

    public const LANGS = [
        'en',
        'ru',
        'uk'
    ];

    private FilmByProviderRepository $filmByProviderRepository;
    private FilmByProviderService $filmByProviderService;
    private ProviderRepository $providerRepository;
    protected TaskService $taskService;
    private ValidatorInterface $validator;
    protected ?CommandTask $task;
    protected FilmFieldInterface $filmFieldService;
    protected FilmFieldTranslateInterface $filmFieldTranslateService;
    protected FilmPeopleInterface $filmPeopleService;
    protected FilmImageInterface $filmImageService;

    /**
     * @param TaskService $taskService
     * @param ValidatorInterface $validator
     * @param ProviderRepository $providerRepository
     * @param FilmByProviderRepository $filmByProviderRepository
     * @param FilmByProviderService $filmByProviderService
     * @throws Exception
     */
    public function __construct(
        TaskService $taskService,
        ValidatorInterface $validator,
        ProviderRepository $providerRepository,
        FilmByProviderRepository $filmByProviderRepository,
        FilmByProviderService $filmByProviderService
    ) {
        $this->__tConstruct();
        $this->taskService = $taskService;
        $this->validator = $validator;
        $this->providerRepository = $providerRepository;
        $this->task = $this->getTask();
        $this->filmFieldService = new ($this->getClassName('FilmFieldService'))($this->validator);
        $this->filmFieldTranslateService = new ($this->getClassName('FilmFieldTranslateService'))($validator);
        $this->filmPeopleService = new ($this->getClassName('FilmPeopleService'))($validator);
        $this->filmImageService = new ($this->getClassName('FilmImageService'))($this->validator);
        $this->filmByProviderService = $filmByProviderService;
        $this->filmByProviderRepository = $filmByProviderRepository;
    }
    abstract protected function getPageCrawler(string $linkByFilms);
    abstract protected function getNextPageLink(string $nextPageToken): string;
    abstract protected function getNextPageToken(?Crawler $crawler, ?string $previousPage = null): string;
    abstract protected function getNodeFilms(?Crawler $crawler): Crawler;

    /**
     * @return void
     * @throws Exception|GuzzleException
     */
    public function parserPages(): void
    {
        $countFilms = $this->countFilmsOnPage;
        $this->taskService->setWorkStatus($this->task);
        $page = $this->firstPage;
        while ($countFilms == $this->countFilmsOnPage) {
//            try {
                $dataFilms = $this->parseFilmsByPage($page);
                $countFilms = $dataFilms['count'];
//            } catch (Exception $e) {
//                $this->taskService->setErrorStatus($this->task, $e->getMessage());
//                throw new Exception($e->getMessage());
//            }
            $page = $dataFilms['nextToken'];
        }
    }

    /**
     * @param string $page
     * @return array
     * @throws Exception|GuzzleException
     */
    #[ArrayShape(['count' => 'int', 'nextToken' => 'string'])]
    protected function parseFilmsByPage(string $page): array
    {
        $nextLink = $this->getNextPageLink($page);
        $crawler = $this->getPageCrawler($nextLink);
        $films = $this->getNodeFilms($crawler)->each(function ($node) {
            if ($this->isItemFilm($node)) {
                $this->addFilmInput($node);
            }
        });

        return ['count' => count($films), 'nextToken' => $this->getNextPageToken($crawler, $page)];
    }

    /**
     * @return string
     * @throws Exception
     */
    protected function getDefaultLink(): string
    {
        if (!$defaultLink = $this->defaultLink) {
            throw new Exception('Link is not found');
        }
        return $defaultLink;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getParserName(): string
    {
        if (!$parserName = $this->parserName) {
            throw new Exception('Parser is not found');
        }
        return $parserName;
    }

    /**
     * @param string $str
     * @return int
     */
    protected function convertTime(string $str): int
    {
        $a = preg_replace("/[^0-9]/", '', $str);
        $time = ((substr($a, 0, 2)) * 60) + (substr($a, -2, 2));
        return $time;
    }

    /**
     * @param $name
     * @return Provider|null
     */
    protected function getProvider($name): ?Provider
    {
        return $this->providerRepository->findOneBy(['name' => $name]);
    }

    /**
     * @return CommandTask|null
     * @throws Exception
     */
    protected function getTask(): ?CommandTask
    {
        return $this->taskService->getTask($this->getProvider($this->getParserName()));
    }

    /**
     * @param $crawlerChild
     * @param $lang
     * @return FilmFieldTranslationInput
     */
    protected function getFilmFieldTranslation($crawlerChild, $lang): FilmFieldTranslationInput
    {
        $imageInput = $this->filmFieldTranslateService->parseBannerTranslate($crawlerChild);
        $description = $this->filmFieldTranslateService->parseDescriptionTranslate($crawlerChild);
        $title = $this->filmFieldTranslateService->parseTitleTranslate($crawlerChild);
        $filmFieldTranslation = new FilmFieldTranslationInput($title, $description, $lang);
        $filmFieldTranslation->setBannersInput($imageInput);
        $this->validator->validate($filmFieldTranslation);

        return $filmFieldTranslation;
    }


    /**
     * @param $nameService
     * @return string|null
     * @throws Exception
     */
    public function getClassName($nameService): ?string
    {
        $className = 'App\Service\Parsers\\' . $this->getParserName() . '\\' . $nameService;
        if (!class_exists($className)) {
            throw new Exception('Class ' . $className . ' not found');
        }
        return $className;
    }

    /**
     * @param FilmInput $filmInput
     * @param $crawlerChild
     * @param string $lang
     * @return FilmInput
     */
    protected function parseFilmByProvider(
        FilmInput $filmInput,
        $crawlerChild,
        string $lang = self::LANG_DEFAULT
    ): FilmInput {
        $filmFieldTranslation = $this->getFilmFieldTranslation($crawlerChild, $lang);
        $filmInput->addFilmFieldTranslationInput($filmFieldTranslation);

        if ($lang === self::LANG_DEFAULT) {
            $filmInput->setAge($this->filmFieldService->parseAge($crawlerChild));
            $filmInput->setRating($this->filmFieldService->parseRating($crawlerChild));
            $filmInput->setYears($this->filmFieldService->parseYear($crawlerChild));
            $filmInput->setDuration($this->filmFieldService->parseDuration($crawlerChild));
            $filmInput->setCountriesInput($this->filmFieldService->parseCountry($crawlerChild));
            $filmInput->setGenresInput($this->filmFieldService->parseGenre($crawlerChild));
            $filmInput->setDirectorsInput($this->filmPeopleService->parseDirector($crawlerChild));
            $filmInput->setCastsInput($this->filmPeopleService->parseCast($crawlerChild));
            $filmInput->setAudiosInput($this->filmFieldService->parseAudio($crawlerChild));
        }

        sleep(rand(0, 3));

        return $filmInput;
    }

    /**
     * @param $node
     * @return void
     * @throws GuzzleException
     * @throws Exception
     */
    protected function addFilmInput($node): void
    {
        if ($this->task->getStatus() == 0) {
            throw new Exception('Task is stop manual.');
        }
        $filmInput = new FilmInput();
        $linkFilm = $this->filmFieldService->parseLink($node);
        $filmInput->setLink($linkFilm);
        $movieId = $this->filmFieldService->parseFilmId($linkFilm);
        $posterInput = $this->filmImageService->parseImage($node);
        $filmInput->setImagesInput($posterInput);
        $filmInput->setMovieId((int)$movieId);
        $provider = $this->getProvider($this->getParserName());
        $filmInput->setProvider($provider);
        $film = $this->filmByProviderRepository->findOneBy(['movieId' => $movieId]);
        if (!$film) {
            foreach (self::LANGS as $lang) {
                $htmlChild = $this->getContentLink($linkFilm, $lang, self::LANG_DEFAULT, $this->getCookies($lang));
                $crawlerChild = $this->getCrawler($htmlChild);
                if ($crawlerChild->filter('h1')->text() == 'Movies') {
                    return;
                }
                $filmInput = $this->parseFilmByProvider($filmInput, $crawlerChild, $lang);
            }
            $this->validator->validate($filmInput);
            $film = $this->filmByProviderService->addFilmByProvider($filmInput);
        }
        $this->taskService->updateTask($film, $this->task);
    }

    protected function isItemFilm(Crawler $node): bool
    {
        return true;
    }

    protected function getCookies(): ?CookieJar
    {
        return null;
    }
}
