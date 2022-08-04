<?php

declare(strict_types=1);

namespace App\Service\Parsers;

use App\DTO\FilmInput;
use App\DTO\AudioInput;
use App\DTO\CountryInput;
use App\DTO\PeopleInput;
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
 * Class MegogoService
 */
class MegogoService extends MainParserService
{
    public string $parserName = Provider::MEGOGO;
    public string $defaultLink = 'https://megogo.net/en/search-extended?category_id=16&main_tab=filters&sort=add&ajax=true&origin=/en/search-extended?category_id=16&main_tab=filters&sort=add&widget=widget_58';

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
     * @return void
     * @throws Exception
     */
    public function runExec(): void
    {
        $this->exec($this->getDefaultLink(), $this->getParserName());
    }

    /**
     * @return void
     * @throws GuzzleException
     * @throws Exception
     */
    protected function parserPages($linkByFilms = null): void
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
        $crawler = $this->getPageCrawler($linkByFilms);
        $crawler->filter('div.thumbnail div.thumb a')->each(function ($node) {

            if (
                !str_contains($node->link()->getUri(), 'treyler')
                and !str_contains($node->link()->getUri(), 'trailer')
            ) {
                $this->addFilmInput($node);
            }
        });
        $this->parseFilmsByPage($this->getNextPageLink($this->getNextPageToken($crawler)));
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
     * @param $linkByFilms
     * @return Crawler
     */
    protected function getPageCrawler($linkByFilms, $page = null): Crawler
    {
        $html = $this->getContentLink($linkByFilms);
        if ($linkByFilms === $this->defaultLink) {
            $html = str_replace('\"', '', $html);
        }
        return $this->getCrawler($html);
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
    protected function parseDirector($crawler): ArrayCollection
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
        $htmls = $this->getContentLink('https://megogo.net' . $link);
        return $this->getCrawler($htmls);
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
        $filmInput->setDirectorsInput($this->parseDirector($crawler));
        $filmInput->setCastsInput($this->getCastActor($crawler));
    }

    /**
     * @param $linkFilm
     * @return ArrayCollection
     * @throws GuzzleException
     */
    protected function parseImage($linkFilm): ArrayCollection
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
     * @return string|null
     */
    protected function parseTitleTranslate($crawlerChild): ?string
    {
        $data = $crawlerChild->filterXpath("//meta[@property='og:title']")->extract(['content']);
        $title = $data[0];
        return $title;
    }

    /**
     * @param $crawlerChild
     * @return string|null
     */
    protected function parseDescriptionTranslate($crawlerChild): ?string
    {
        return $crawlerChild->filter('div.video-description')->text();
    }

    /**
     * @param $crawlerChild
     * @return ImageInput
     */
    protected function parseBannerTranslate($crawlerChild): ImageInput
    {
        $bannerLink = $crawlerChild->filter('div.thumbnail div.thumb img')->image()->getUri();
        return $this->getImageInput($bannerLink);
    }

    /**
     * @param $crawlerChild
     * @return string|null
     */
    protected function parseYear($crawlerChild): ?string
    {
        return $crawlerChild->filter('span.video-year')->text();
    }

    /**
     * @param $crawlerChild
     * @return int
     */
    protected function parseDuration($crawlerChild): ?int
    {
        $duration = (int)(preg_replace(
            "/[^,.0-9]/",
            '',
            $crawlerChild->filter(' div.video-duration span')->text()
        ));
        return  $duration;
    }
}
