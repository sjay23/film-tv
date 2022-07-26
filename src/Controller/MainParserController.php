<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CommandTaskRepository;
use Symfony\Bundle\FrameworkBundle\Console\Application;

/**
 * @Route("/")
 */
class MainParserController extends AbstractController
{
    /**
     * @Route("/", name="main", methods={"GET", "POST"})
     */
    public function index(CommandTaskRepository $taskRepository): Response
    {

        return $this->renderForm('parser/mainParserPage.html.twig', [
            'sweetTvTask' => $taskRepository->findOneBy(['provider' => 1]),
        ]);
    }

    /**
     * @Route("/sweetTv/film/parse", name="sweetTv_film_parse", methods={"GET", "POST"})
     */
    public function sweetTvParse(KernelInterface $kernel): Response
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);
        $input = new ArrayInput([
            'command' => 'php bin/console app:sweet-tv-parser',
        ]);
        $output = new BufferedOutput();
        $application->run($input, $output);

        return $this->redirectToRoute('admin');
    }

}