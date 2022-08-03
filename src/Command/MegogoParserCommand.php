<?php

namespace App\Command;


use App\Service\Parsers\MegogoService;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MegogoParserCommand extends Command
{
    protected static $defaultName = 'app:megogo-parser';

    /**
     * @var MegogoService
     */
    private MegogoService $megogoService;

    public function __construct(
        MegogoService $megogoService
    ) {
        $this->megogoService = $megogoService;
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    /**
     * @throws GuzzleException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Start parser Megogo',
            '============',
            '',
        ]);
        $this->megogoService->runExec();

        return Command::SUCCESS;
    }
}
