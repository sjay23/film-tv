<?php

namespace App\Command;

use App\Repository\ProviderRepository;
use App\Service\Parsers\ExecParserService;
use App\Service\Parsers\MegogoService;
use App\Service\Parsers\SweetTvService;
use App\Service\TaskService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class ParserCommand extends Command
{
    protected static $defaultName = 'app:parser';

    /**
     * @var MegogoService
     */
    private MegogoService $megogoService;

    /**
     * @var SweetTvService
     */
    private SweetTvService $sweetTvService;

    public function __construct(
        TaskService $taskService,
        SweetTvService $sweetTvService,
        MegogoService $megogoService,
        ProviderRepository $providerRepository,

    ) {
        $this->taskService = $taskService;
        $this->sweetTvService = $sweetTvService;
        $this->megogoService = $megogoService;
        $this->providerRepository = $providerRepository;
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            'Please select Provider',
            [$this->megogoService->getParserName(),$this->sweetTvService->getParserName()],
        );
        $name = $helper->ask($input, $output, $question);
        $output->writeln([
            'Start parser ' . $name ,
            '============',
            '',
        ]);
        switch ($name) {
            case $this->megogoService->getParserName():
                $class = new ExecParserService($this->megogoService, $this->taskService ,$this->providerRepository);
                $class->exec();
                break;
            case $this->sweetTvService->getParserName():
                $class = new ExecParserService($this->sweetTvService, $this->taskService,$this->providerRepository);
                $class->exec();
                break;
        }

        return Command::SUCCESS;
    }
}
