<?php

namespace App\Command;


use App\Service\Parsers\MainParserService;
use App\Service\Parsers\MegogoService;
use App\Service\Parsers\SweetTvService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

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

        SweetTvService $sweetTvService,
        MegogoService $megogoService
    ) {

        $this->sweetTvService = $sweetTvService;
        $this->megogoService = $megogoService;
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
                $this->megogoService->exec();
                break;
            case $this->sweetTvService->getParserName():
                $this->sweetTvService->exec();
                break;
        }

        return Command::SUCCESS;
    }
}
