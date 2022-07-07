<?php

namespace App\Controller;


use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestsController extends AbstractController
{
    /**
     * @Route("/", name="main", methods={"GET"})
     */
    public function index(  ): Response
    {
        $client = new Client();
        $response = $client->get('https://sweet.tv/en/movie/10565-hercules');
        $html = (string) $response->getBody();

        $crawler = new Crawler($html);
        dd($crawler);
        return $this->render('main/mainPage.html.twig');
    }



}