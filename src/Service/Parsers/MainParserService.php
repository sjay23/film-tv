<?php

namespace App\Service\Parsers;

use App\DTO\FilmFieldTranslationInput;
use App\DTO\FilmInput;
use App\Entity\CommandTask;
use App\Entity\Provider;
use App\Repository\FilmByProviderRepository;
use App\Repository\ProviderRepository;
use App\Service\TaskService;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class MainParserService
{
    protected const LANG_DEFAULT = 'en';

    public const LANGS = [
        'en',
        'ru',
        'uk'
    ];

    /**
     * @var FilmByProviderRepository
     */
    private FilmByProviderRepository $filmByProviderRepository;

    /**
     * @var FilmByProviderService
     */
    private FilmByProviderService $filmByProviderService;

    /**
     * @var Client
     */
    protected Client $client;

    /**
     * @var ProviderRepository
     */
    protected ProviderRepository $providerRepository;

    /**
     * @var TaskService
     */
    protected TaskService $taskService;

    /**
     * @var ValidatorInterface
     */
    protected ValidatorInterface $validator;
    protected ?CommandTask $task;
    protected string $parserName;
    public string $defaultLink;

    /**
     * @param TaskService $taskService
     * @param ValidatorInterface $validator
     * @param ProviderRepository $providerRepository
     */
    protected function __construct(
        TaskService $taskService,
        ValidatorInterface $validator,
        ProviderRepository $providerRepository,
        FilmByProviderRepository $filmByProviderRepository,
        FilmByProviderService $filmByProviderService
    ) {
        $this->taskService = $taskService;
        $this->validator = $validator;
        $this->providerRepository = $providerRepository;
        $this->task = $this->getTask($this->parserName);
        $this->client = new Client();
        $this->filmByProviderService = $filmByProviderService;
        $this->filmByProviderRepository = $filmByProviderRepository;
    }

    abstract protected function parseAge(Crawler $crawler);
    abstract protected function parseRating(Crawler $crawler);
    abstract protected function parseImage(string $linkFilm): ArrayCollection;
    abstract protected function parseYear(Crawler $crawlerChild);
    abstract protected function parseDuration(Crawler $crawlerChild);
    abstract protected function parseCountry(Crawler $crawler): ArrayCollection;
    abstract protected function parseAudio(Crawler $crawler): ArrayCollection;
    abstract protected function parseDirector(Crawler $crawler): ArrayCollection;
    abstract protected function parseGenre(Crawler $crawler): ArrayCollection;
    abstract protected function parseFilmId(string $linkFilm);
    abstract protected function parseTitleTranslate(Crawler $crawlerChild);
    abstract protected function parseDescriptionTranslate(Crawler $crawlerChild);
    abstract protected function parseBannerTranslate(Crawler $crawlerChild);
    abstract protected function getPageCrawler(string $linkByFilms, int $page);
    abstract protected function parserPages(string $linkByFilms);
    abstract public function getParserName();
    abstract public function getDefaultLink();

    /**
     * @return void
     * @throws Exception
     */
    public function exec(): void
    {
        $this->taskService->updateCountTask($this->getTask($this->parserName));
        if ($this->task->getStatus() == 1) {
            throw new Exception('Task is running.');
        }
        $this->taskService->setWorkStatus($this->getTask($this->parserName));
        $this->parserPages($this->defaultLink);
        $this->taskService->setNotWorkStatus($this->getTask($this->parserName));
    }

    /**
     * @param string $link
     * @param string $lang
     * @return string|null
     * @throws GuzzleException
     */
    protected function getContentLink(string $link, string $lang = self::LANG_DEFAULT): ?string
    {
        sleep(rand(0, 3));
        if ($lang !== self::LANG_DEFAULT) {
            $link = str_replace(self::LANG_DEFAULT, $lang, $link);
        }
        echo 'Parse link: ' . $link . "\n";
        $response = $this->client->get($link);

        return (string)$response->getBody();
    }

    /**
     * @param string $html
     * @return Crawler
     */
    protected function getCrawler(string $html): Crawler
    {
        return new Crawler($html);
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
     * @param $name
     * @return CommandTask|null
     */
    protected function getTask($name): ?CommandTask
    {
        return $this->taskService->getTask($this->getProvider($name));
    }

    /**
     * @param $crawlerChild
     * @param $lang
     * @return FilmFieldTranslationInput
     */
    protected function getFilmFieldTranslation($crawlerChild, $lang): FilmFieldTranslationInput
    {
        $imageInput = $this->parseBannerTranslate($crawlerChild);
        $description = $this->parseDescriptionTranslate($crawlerChild);
        $title = $this->parseTitleTranslate($crawlerChild);
        $filmFieldTranslation = new FilmFieldTranslationInput($title, $description, $lang);
        $filmFieldTranslation->setBannersInput($imageInput);
        $this->validator->validate($filmFieldTranslation);

        return $filmFieldTranslation;
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
            $filmInput->setRating((float)$this->filmFieldService->parseRating($crawlerChild));
            $filmInput->setYears((int)$this->filmFieldService->parseYear($crawlerChild));
            $filmInput->setDuration($this->filmFieldService->parseDuration($crawlerChild));
            $filmInput->setCountriesInput($this->filmFieldService->parseCountry($crawlerChild));
            $filmInput->setGenresInput($this->filmFieldService->parseGenre($crawlerChild));
            $filmInput->setDirectorsInput($this->filmPeopleService->parseDirector($crawlerChild));
            $filmInput->setCastsInput($this->filmPeopleService->parseCast($crawlerChild,$filmInput));
            $filmInput->setAudiosInput($this->filmFieldService->parseAudio($crawlerChild));
        }

        sleep(rand(0, 3));

        return $filmInput;
    }

    /**
     * @param $node
     * @return void
     */
    protected function addFilmInput($node): void
    {
        if ($this->task->getStatus() == 0) {
            throw new Exception('Task is stop manual.');
        }
        $filmInput = new FilmInput();
        $linkFilm = $node->link()->getUri();
        $filmInput->setLink($linkFilm);
        $movieId = $this->parseFilmId($linkFilm);
        $posterInput = $this->parseImage($node);
        $filmInput->setImagesInput($posterInput);
        $filmInput->setMovieId((int)$movieId);
        $provider = $this->getProvider($this->getParserName());
        $filmInput->setProvider($provider);
        $film = $this->filmByProviderRepository->findOneBy(['movieId' => $movieId]);
        if (!$film) {
            foreach (self::LANGS as $lang) {
                $htmlChild = $this->getContentLink($linkFilm, $lang);
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
}
