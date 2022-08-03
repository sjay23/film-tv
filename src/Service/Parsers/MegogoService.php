<?php

declare(strict_types=1);

namespace App\Service\Parsers;

use App\DTO\FilmFieldTranslationInput;
use App\DTO\FilmInput;
use App\DTO\AudioInput;
use App\DTO\CountryInput;
use App\DTO\PeopleInput;
use App\DTO\ImageInput;
use App\Entity\CommandTask;
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
 * Class MegogoService
 */
class MegogoService extends MainParserService
{
    /**
     * @var TaskService
     */
    private TaskService $taskService;

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
     * @param FilmByProviderRepository $filmByProviderRepository
     * @param FilmByProviderService $filmByProviderService
     */
    public function __construct(
        TaskService $taskService,
        ValidatorInterface $validator,
        FilmByProviderRepository $filmByProviderRepository,
        ProviderRepository $providerRepository,
        FilmByProviderService $filmByProviderService
    ) {
        parent::__construct($taskService, $providerRepository);
        $this->taskService = $taskService;
        $this->task = $this->getTask(Provider::MEGOGO);
        $this->validator = $validator;
        $this->filmByProviderService = $filmByProviderService;
        $this->filmByProviderRepository = $filmByProviderRepository;
        $this->defaultLink = 'https://megogo.net/en/search-extended?category_id=16&main_tab=filters&sort=add&ajax=true&origin=/en/search-extended?category_id=16&main_tab=filters&sort=add&widget=widget_58';
    }

    /**
     * @return void
     * @throws GuzzleException
     * @throws Exception
     */
    public function runExec(): void
    {
        $this->exec($this->defaultLink, Provider::MEGOGO);
    }

    /**
     * @return void
     * @throws GuzzleException
     * @throws Exception
     */
    public function parserPages(): void
    {
        try {
            $this->parseFilmsByPage($this->defaultLink);
        } catch (Exception $e) {
            $this->taskService->setErrorStatus($this->task, $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param string $linkByFilms
     * @return void
     * @throws GuzzleException
     * @throws Exception
     */
    protected function parseFilmsByPage(string $linkByFilms): void
    {
        $html = $this->getContentLink($linkByFilms);
        if ($linkByFilms === $this->defaultLink) {
            $html = str_replace('\"', '', $html);
        }
            $crawler = $this->getCrawler($html);
        $crawler->filter('div.thumbnail div.thumb a')->each(function ($node) {

            if (
                !str_contains($node->link()->getUri(), 'treyler')
                and !str_contains($node->link()->getUri(), 'trailer')
            ) {
                if ($this->task->getStatus() == 0) {
                    throw new Exception('Task is stop manual.');
                }
                $filmInput = new FilmInput();
                $linkFilm = $node->link()->getUri();
                $filmInput->setLink($linkFilm);
                $posterInput = $this->parseImage($linkFilm);
                $filmInput->setImagesInput($posterInput);
                $movieId = $this->parseFilmId($linkFilm);
                $filmInput->setMovieId((int)$movieId);
                $provider = $this->getProvider(Provider::MEGOGO);
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
                $this->taskService->updateTask($film, $this->task);
            }
        });
        $this->parseFilmsByPage($this->getNextPageLink($this->getNextPageToken($crawler)));
    }

    /**
     * @param FilmInput $filmInput
     * @param $crawlerChild
     * @param string $lang
     * @return FilmInput
     * @throws GuzzleException
     */
    private function parseFilmByMegogo(
        FilmInput $filmInput,
        $crawlerChild,
        string $lang = self::LANG_DEFAULT
    ): FilmInput {
        $filmFieldTranslation = $this->getFilmFieldTranslation($crawlerChild, $lang);
        $filmInput->addFilmFieldTranslationInput($filmFieldTranslation);
        if ($lang === self::LANG_DEFAULT) {
            $age = $this->parseAge($crawlerChild);
            $years = $crawlerChild->filter('span.video-year')->text();
            $duration = (int)(preg_replace("/[^,.0-9]/", '', $crawlerChild->filter(' div.video-duration span')->text()));
            $rating = $this->parseRating($crawlerChild);
            $filmInput->setAge($age);
            $filmInput->setRating((float)$rating);
            $filmInput->setYears((int)$years);
            $filmInput->setDuration($duration);
            $countriesCollect = $this->parseCountry($crawlerChild);
            $filmInput->setCountriesInput($countriesCollect);
            $genreCollect = $this->parseGenre($crawlerChild);
            $filmInput->setGenresInput($genreCollect);
            $this->parseCast($crawlerChild, $filmInput);
            $audioCollect = $this->parseAudio($crawlerChild);
            $filmInput->setAudiosInput($audioCollect);
        }

        sleep(rand(0, 3));

        return $filmInput;
    }

    /**
     * @param $crawler
     * @return string
     */
    private function getNextPageToken($crawler): string
    {
        return $crawler->filter('div.pagination-more a.link-gray ')->attr('data-page-more');
    }

    /**
     * @param $nextPageToken
     * @return string
     */
    private function getNextPageLink($nextPageToken): string
    {
        return str_replace('TOKEN', $nextPageToken, $this->defaultLink);
    }

    /**
     * @param string $linkFilm
     * @return string
     */
    protected function parseFilmId($linkFilm): string
    {

        $re = '/https:\/\/megogo.net\/en\/view\/([0-9]*)-(.*)/';
        preg_match($re, $linkFilm, $matches, PREG_OFFSET_CAPTURE, 0);
        return $matches[1][0];
    }



    /**
     * @param $crawler
     * @return ArrayCollection
     */
    protected function parseGenre($crawler): ArrayCollection
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

    private function getImageInput(string $link): ImageInput
    {
        $imageInput = new ImageInput($link);
        $this->validator->validate($imageInput);
        return $imageInput;
    }

    /**
     * @param $crawler
     * @return ArrayCollection
     */
    protected function parseAudio($crawler): ArrayCollection
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
    protected function parseCountry($crawler): ArrayCollection
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
    private function getCastDirector($crawler): ArrayCollection
    {
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
     * @return ArrayCollection
     */
    private function getCastActor($crawler): ArrayCollection
    {
        $castGenre = $crawler->filter('div.video-persons .type-main a.link-default')->each(function (Crawler $node) {
            $link = 'https://megogo.net' . $node->attr('href');
            $name = $node->filter('div.video-person-name')->text();
            $castInput = new PeopleInput($name, $link);
            $this->validator->validate($castInput);
            return $castInput;
        });

        return new ArrayCollection($castGenre);
    }

    /**
     * @param $crawler
     * @return Crawler
     * @throws GuzzleException
     */
    private function getCastCrawler($crawler): Crawler
    {
        $link = $crawler->filter('ul.video-view-tabs')->children('.nav-item')->eq(1)->children('a')->attr('href');
        $html = $this->getContentLink('https://megogo.net' . $link);
        return $this->getCrawler($html);
    }

    /**
     * @param $crawler
     * @param $filmInput
     * @return void
     * @throws GuzzleException
     */
    protected function parseCast($crawler, $filmInput): void
    {
        $crawler = $this->getCastCrawler($crawler);
        $filmInput->setDirectorsInput($this->getCastDirector($crawler));
        $filmInput->setCastsInput($this->getCastActor($crawler));
    }

    /**
     * @param $linkFilm
     * @return ArrayCollection
     * @throws GuzzleException
     */
    protected function parseImage($linkFilm): ArrayCollection
    {
        $link = $this->getCrawler($this->getContentLink($linkFilm))
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

    /**
     * @param $crawler
     * @return string|null
     */
    protected function parseRating($crawler): ?string
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
    protected function parseAge($crawler): ?string
    {
        return $crawler->filter('.videoInfoPanel-age-limit')->text();
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
