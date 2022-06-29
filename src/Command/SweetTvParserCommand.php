<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class SweetTvParserCommand extends Command
{
    protected static $defaultName = 'app:sweet-tv-parser';

    /**
     * @var Client
     */
    private Client $client;

    public function __construct() {
        $this->client = new Client();
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Start parser Sweet tv',
            '============',
            '',
        ]);
        $response = $this->client->get('https://sweet.tv/en/movies/all-movies/sort=5');
        $html = (string) $response->getBody();

        $crawler = new Crawler($html);
        $crawler->filter('.movie__item-link')->each(function ($node) {
            $filmData = [];
            $link = $node->link()->getUri();
            $filmData['link']['en'] = $link;
            $filmData['title']['en'] = $node->filter('h4')->text();
            $poster = $node->filter('img')->image()->getUri();
            $filmData['poster']['en'] = $poster;
            sleep(rand(0,3));
            $responseChild = $this->client->get($link);
            $htmlChild = (string) $responseChild->getBody();
            $crawlerChild = new Crawler($htmlChild);
            $filmData['year'] = $crawlerChild->filter('.film__years > .film-left__details')->text();

            dump($filmData);
            die();
        });
        die();
        $output->writeln([
            'Finish Parser',
            '============',
        ]);
        return Command::SUCCESS;
    }
}
