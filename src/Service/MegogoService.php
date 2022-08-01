<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\FilmFieldTranslationInput;
use App\DTO\FilmInput;
use App\DTO\AudioInput;
use App\DTO\CountryInput;
use App\DTO\PeopleInput;
use App\DTO\GenreInput;
use App\DTO\ImageInput;
use App\Entity\CommandTask;
use App\Entity\Provider;
use App\Repository\ProviderRepository;
use App\Repository\CommandTaskRepository;
use App\Repository\FilmByProviderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class MegogoService
 */
class MegogoService
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
        $this->client = new Client();
    }

    /**
     * @return void
     * @throws GuzzleException
     * @throws Exception
     */
    public function exec(): void
    {
        $linkByFilms = 'https://megogo.net/en/search-extended?category_id=16&main_tab=filters&sort=add&ajax=true&origin=/en/search-extended?category_id=16&main_tab=filters&sort=add&widget=widget_58';
        $html = $this->getContentLink($linkByFilms);
        $a = Str_replace('\"', '', $html);
        $crawler = $this->getCrawler($a);
        $nextPageToken = $this->getNextPageToken($crawler);
        $this->taskService->updateCountTask($this->getTask());
        if ($this->getTask()->getStatus() == 1) {
            throw new Exception('Task is running.');
        }
        $this->taskService->setWorkStatus($this->getTask());

        try {
            $this->parseFilmsByPage($nextPageToken);
        } catch (Exception $e) {
            $this->taskService->setErrorStatus($this->getTask(), $e->getMessage());
            throw new Exception($e->getMessage());
        }


        $this->taskService->setNotWorkStatus($this->getTask());
    }

    /**
     * @param string $nextPageToken
     * @return void
     * @throws GuzzleException
     * @throws Exception
     */
    private function parseFilmsByPage( string $nextPageToken): void
    {
        $defaultLink='https://megogo.net/en/search-extended?category_id=16&main_tab=filters&pageToken=TOKEN&sort=add&ajax=true&origin=/en/search-extended?category_id=16&main_tab=filters&sort=add&widget=widget_58';
        $link = str_replace('TOKEN', $nextPageToken, $defaultLink);
        $html = $this->getContentLink($link);
        $crawler = $this->getCrawler($html);
            $crawler->filter('div.thumbnail div.thumb a')->each(function ($node) {
                if ($this->getTask()->getStatus() == 0) {
                    throw new Exception('Task is stop manual.');
                }
                $filmInput = new FilmInput();
                $linkFilm = $node->link()->getUri();
                $filmInput->setLink($linkFilm);
                $movieId = $this->getFilmId($linkFilm);
                $filmInput->setMovieId((int)$movieId);
                $posterInput = $this->parseImage($node);
                $filmInput->addImageInput($posterInput);
                $provider = $this->getProvider();
                $filmInput->setProvider($provider);
                $film = $this->filmByProviderRepository->findOneBy(['movieId' => $movieId]);
                if (!$film) {
                    foreach (self::LANGS as $lang) {
                        $htmlChild = $this->getContentLink($linkFilm, $lang);
                        $crawlerChild = $this->getCrawler($htmlChild);
                        if ($crawlerChild->filter('h1')->text() == 'Movies') {
                            return;
                        }
                        $filmInput = $this->parseFilmByMegogo($filmInput, $crawlerChild, $lang);
                    }
                    $this->validator->validate($filmInput);
                    $film = $this->filmByProviderService->addFilmByProvider($filmInput);
                }
                $this->taskService->updateTask($film, $this->getTask());
            });
            $this->parseFilmsByPage( $this->getNextPageToken($crawler));
    }

    /**
     * @param FilmInput $filmInput
     * @param $crawlerChild
     * @param string $lang
     * @return FilmInput
     */
    private function parseFilmByMegogo(FilmInput $filmInput, $crawlerChild, string $lang = self::LANG_DEFAULT): FilmInput
    {
        $filmFieldTranslation = $this->getFilmFieldTranslation($crawlerChild, $lang);
        $filmInput->addFilmFieldTranslationInput($filmFieldTranslation);

        if ($lang === self::LANG_DEFAULT) {
            $age = $this->parseAge($crawlerChild);
            $years = $crawlerChild->filter('span.video-year')->text();
            $duration =  preg_replace("/[^,.0-9]/", '', $crawlerChild->filter(' div.video-duration span')->text());
            $rating = $this->parseRating($crawlerChild);
            $filmInput->setAge($age);
            $filmInput->setRating((float)$rating);
            $filmInput->setYears((int)$years);
            $filmInput->setDuration($duration);
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
     * @return string
     */
    private function getNextPageToken($crawler): int
    {
        return $crawler->filter('div.pagination-more a.link-gray ')->attr('data-page-more');
    }



    /**
     * @param string $linkFilm
     * @return string
     */
    private function getFilmId(string $linkFilm): string
    {

        $re = '/https:\/\/megogo.net\/en\/view\/([0-9]*)-(.*)/';
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
        return $this->providerRepository->findOneBy(['name' => Provider::MEGOGO]);
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
        $data = $crawler->filterXpath("//meta[@property='ya:ovs:genre']")->extract(['content']);
        $genres = explode(',', $data[0]);
        $filmGenre = [];
        foreach ($genres as $genre) {
            $genresInput = new CountryInput($genre);
            $this->validator->validate($genresInput);
            $filmGenre[] = $genresInput;
        }
        return new ArrayCollection($filmGenre);
    }

    /**
     * @param $crawler
     * @return ArrayCollection
     */
    private function parseAudio($crawler): ArrayCollection
    {
        $data = $crawler->filterXpath("//meta[@property='ya:ovs:languages']")->extract(['content']);
        $audios = explode(',', $data[0]);
        $filmAudio = [];
        foreach ($audios as $audio) {
            $audioInput = new AudioInput($audio);
            $this->validator->validate($audioInput);
            $filmAudio[] = $audioInput;
        }
        return new ArrayCollection($filmAudio);
    }


    /**
     * @param $crawler
     * @return ArrayCollection
     */
    private function parseCountry($crawler): ArrayCollection
    {
        $data = $crawler->filterXpath("//meta[@property='ya:ovs:country']")->extract(['content']);
        $countries = explode(',', $data[0]);
        $filmCountry = [];
        foreach ($countries as $country) {
            $countriesInput = new CountryInput($country);
            $this->validator->validate($countriesInput);
            $filmCountry[] = $countriesInput;
        }
        return new ArrayCollection($filmCountry);
    }

    /**
     * @param $crawler
     * @return ArrayCollection
     */
    private function parseCast($crawler): ArrayCollection
    {
        $link = $crawler->filter('ul.video-view-tabs')->children('.nav-item')->eq(1)->children('a')->attr('href');
        $html = $this->getContentLink('https://megogo.net' . $link);
        $crawler = $this->getCrawler($html);
        $castGenre = $crawler->filter('div.video-persons a.link-default')->each(function (Crawler $node) {
            $link= 'https://megogo.net' . $node->attr('href');
            $name = $node->filter('div.video-person-name')->text();
            $castInput = new PeopleInput($name, $link);
            $this->validator->validate($castInput);
            return $castInput;
        });

        return new ArrayCollection($castGenre);
    }

    /**
     * @param $crawler
     * @return ArrayCollection
     */
    private function parseDirector($crawler): ArrayCollection
    {
        $link = $crawler->filter('ul.video-view-tabs')->children('.nav-item')->eq(1)->children('a')->attr('href');
        $html = $this->getContentLink('https://megogo.net' . $link);
        $crawler = $this->getCrawler($html);
        $directors = [];
        $directorName = $crawler->filter('a[itemprop="director"] div')->text();
        $data = $crawler->filter('a[itemprop="director"]')->attr('href');
        $directorLink = 'https://megogo.net' . $data;
        $directorInput = new PeopleInput($directorName, $directorLink);
        $this->validator->validate($directorInput);
        $directors[] = $directorInput;
        return new ArrayCollection($directors);
    }

    /**
     * @param $crawler
     * @return ImageInput
     */
    private function parseImage($crawler): ImageInput
    {
        $imageLink = $crawler->filter('div.thumbnail div.thumb a img')->image()->getUri();

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
        $node = $crawler->filter('.videoInfoPanel-rating');
        if ($node->count() !== 0) {
            $rating = $node->filter('span.value')->text();
        }

        return $rating;
    }

    /**
     * @param $crawler
     * @return string|null
     */
    private function parseAge($crawler): ?string
    {
        return $crawler->filter('.videoInfoPanel-age-limit');
    }

    /**
     * @return CommandTask|null
     */
    private function getTask(): ?CommandTask
    {
        return $this->taskService->getTask($this->getProvider());
    }

    /**
     * @param $crawlerChild
     * @param $lang
     * @return FilmFieldTranslationInput
     */
    private function getFilmFieldTranslation($crawlerChild, $lang): FilmFieldTranslationInput
    {
        $data = $crawlerChild->filterXpath("//meta[@property='og:title']")->extract(['content']);
        $title = $data[0];
        $description = $crawlerChild->filter('div.video-description')->text();
        $bannerLink = $crawlerChild->filter('div.thumbnail div.thumb img')->image()->getUri();
        $imageInput = $this->getImageInput($bannerLink);
        $filmFieldTranslation = new FilmFieldTranslationInput($title, $description, $lang);
        $filmFieldTranslation->setBannersInput($imageInput);
        $this->validator->validate($filmFieldTranslation);

        return $filmFieldTranslation;
    }
}
