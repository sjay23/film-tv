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
            $poster = $node->filter('.movie__item-img > img.img_wauto_hauto')->image()->getUri();
            $filmData['poster']['en'] = $poster;
            sleep(rand(0,3));
            $responseChild = $this->client->get($link);
            $htmlChild = (string) $responseChild->getBody();
            $crawlerChild = new Crawler($htmlChild);
            $filmData['year'] = $crawlerChild->filter('.film__years > .film-left__details')->text();
            $filmData['desciption']['en'] = $crawlerChild->filter('p.film-descr__text')->text();
            $filmData['rating'] = $crawlerChild->filter('.film__rating > .film-left__details > span')->text();
            $filmData['country'] = $crawlerChild->filter('div.film__countries a.film-left__link')->text();
            $filmData['genre'] = $crawlerChild->filter('div.film__genres a:nth-child(2)')->text();
            $filmData['director']['name'] = $crawlerChild->filter('div.film__directors span')->text();
            $filmData['director']['link'] = $crawlerChild->filter('div.film__directors  a')->link()->getUri();
            $filmData['cast'] = $crawlerChild->filter('div.film__actor a')->each(function (Crawler $node) {
                return [ 'name' => $node->text(), 'link' => $node->link()->getUri() ];});
            $filmData['age'] =  $crawlerChild->filter('div.film__age div.film-left__details div.film-left__flex ')->text();
            $filmData['duration'] = $crawlerChild->filter(' span.film-left__time')->text();
            $filmData['banner']['en'] = $crawlerChild->filter('div.film-right  div.film-right__img picture img')->image()->getUri();
            $filmData['audio'] =  array_unique($crawlerChild->filter('div.film__sounds div.film__content a.film-audio__link span')->each(function (Crawler $node) {

                return $node->text();
            }));

            dump($filmData);
            die();
        });
        die();

        return Command::SUCCESS;
    }
}
