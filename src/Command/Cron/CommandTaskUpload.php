<?php

namespace App\Command\Cron;

use App\Entity\Provider;
use App\Repository\ProviderRepository;
use App\Repository\FilmByProviderRepository;
use App\Service\FilmByProviderService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CommandTaskUpload extends Command
{
    protected static $defaultName = 'cron:upload_images';

    /**
     * @var FilmByProviderService
     */
    private FilmByProviderService $filmByProviderService;

    /**
     * @var ProviderRepository
     */
    private ProviderRepository $providerRepository;

    /**
     * @var FilmByProviderRepository
     */
    private FilmByProviderRepository $filmByProviderRepository;


    public function __construct(
        FilmByProviderService $filmByProviderService,
        ProviderRepository $providerRepository,
        FilmByProviderRepository $filmByProviderRepository
    ) {
        parent::__construct();
        $this->filmByProviderService = $filmByProviderService;
        $this->filmByProviderRepository = $filmByProviderRepository;
        $this->providerRepository = $providerRepository;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $provider = $this->providerRepository->findOneBy(['name' => Provider::SWEET_TV]);
        $films = $this->filmByProviderRepository->getFilmByNoUploadedImage($provider->getId());
        foreach ($films as $film) {
            $this->filmByProviderService->uploadPoster($film);
            $this->filmByProviderService->uploadBanner($film);
        }
        $output->writeln('Done!');

        return Command::SUCCESS;
    }
}
