<?php

namespace App\Controller;

use App\Service\TaskService;
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
 * @Route("/admin/parser")
 */
class MainParserController extends AbstractController
{
    public function __construct(
        TaskService $taskService,
        CommandTaskRepository $commandTaskRepository
    )
    {
        $this->taskService = $taskService;
        $this->commandTaskRepository = $commandTaskRepository;
        $this->task = $this->commandTaskRepository->findOneBy(['provider' => 1]);
    }

    /**
     * @Route("/", name="main", methods={"GET", "POST"})
     */
    public function index(CommandTaskRepository $taskRepository): Response
    {

        return $this->renderForm('parser/mainParserPage.html.twig', [
            'sweetTvTask' =>  $this->task,
        ]);
    }

    /**
     * @Route("/sweetTv/film/", name="sweetTv_film_parse", methods={"GET", "POST"})
     */
    public function sweetTvParse(): Response
    {
        exec('php /var/www/html/bin/console app:sweet-tv-parser > /dev/null &');

        return $this->redirectToRoute('main');
    }

    /**
     * @Route("/stop/sweetTv/film/", name="sweetTv_film_parse_stop", methods={"GET", "POST"})
     */
    public function sweetTvParseStop(): Response
    {
        $this->taskService->setNotWorkStatus($this->task);
        return $this->redirectToRoute('main');
    }

}
