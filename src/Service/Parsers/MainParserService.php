<?php

namespace App\Service\Parsers;

use App\Entity\CommandTask;
use App\Entity\Provider;
use App\Repository\ProviderRepository;
use App\Service\TaskService;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DomCrawler\Crawler;

abstract class MainParserService
{

    protected const LANG_DEFAULT = 'en';

    protected const LANGS = [
        'en',
        'ru',
        'uk'
    ];

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
    private TaskService $taskService;

    /**
     * @param ProviderRepository $providerRepository
     */
    protected function __construct(
        TaskService $taskService,
        ProviderRepository $providerRepository,
    )
    {
        $this->taskService = $taskService;
        $this->providerRepository = $providerRepository;
        $this->client = new Client();
    }

    abstract protected function parseAge($crawler);
    abstract protected function parseRating($crawler);
    abstract protected function parseImage($linkFilm);
    abstract protected function parseCountry($crawler);
    abstract protected function parseAudio($crawler);
    abstract protected function parseGenre($crawler);
    abstract protected function parseFilmId($linkFilm);

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
     * @return Provider|null
     */
    protected function getProvider($name): ?Provider
    {
        return $this->providerRepository->findOneBy(['name' => $name]);
    }

    /**
     * @return CommandTask|null
     */
    protected function getTask($name): ?CommandTask
    {
        return $this->taskService->getTask($this->getProvider($name));
    }


    /**
     * @return void
     * @throws GuzzleException
     * @throws Exception
     */
    protected function exec($linkPage, $name): void
    {
        $this->taskService->updateCountTask($this->getTask($name));
        if ($this->task->getStatus() == 1) {
            throw new Exception('Task is running.');
        }
        $this->taskService->setWorkStatus($this->getTask($name));
        $this->parserPages($linkPage);
        $this->taskService->setNotWorkStatus($this->getTask($name));
    }

}