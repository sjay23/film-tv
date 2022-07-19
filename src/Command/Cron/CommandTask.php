<?php

namespace App\Command\Cron;

use App\Entity\Provider;
use App\Repository\ProviderRepository;
use App\Repository\ImageRepository;
use App\Service\ImageFileService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CommandTask extends Command
{
    protected static $defaultName = 'cron:upload_images';

    /**
     * @var ImageFileService
     */
    private ImageFileService $imageFileService;

    /**
     * @var ProviderRepository
     */
    private ProviderRepository $providerRepository;

    /**
     * @var ImageRepository
     */
    private ImageRepository $imageRepository;



    public function __construct(
        ImageFileService $imageFileService,
        ProviderRepository $providerRepository,
        ImageRepository $imageRepository
    ) {
        parent::__construct();
        $this->imageFileService = $imageFileService;
        $this->imageRepository = $imageRepository;
        $this->providerRepository = $providerRepository;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $images = $this->imageRepository->getNoUploadedImage();
        foreach ($images as $image) {
            $this->imageFileService->getUploadFileByUrl($image->getLink());
        }
        $output->writeln('Done!');

        return Command::SUCCESS;
    }
}
