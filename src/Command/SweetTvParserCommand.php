<?php

namespace App\Command;

use App\Service\Parsers\SweetTvService;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SweetTvParserCommand extends Command
{
    protected static $defaultName = 'app:sweet-tv-parser';

    /**
     * @var SweetTvService
     */
    private SweetTvService $sweetTvService;

    public function __construct(
        SweetTvService $sweetTvService
    ) {
        $this->sweetTvService = $sweetTvService;
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
        $output->writeln([
            'Start parser Sweet tv',
            '============',
            '',
        ]);
        $this->sweetTvService->exec();

        return Command::SUCCESS;
    }
}
