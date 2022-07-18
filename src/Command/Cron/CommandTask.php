<?php

namespace App\Command\Cron;

use App\Entity\Provider;
use App\Repository\ProviderRepository;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
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



    public function __construct( ImageFileService $imageFileService,ProviderRepository $providerRepository,ImageRepository $imageRepository)
    {
        parent::__construct();
        $this->imageFileService = $imageFileService;
        $this->imageRepository = $imageRepository;
        $this->providerRepository = $providerRepository;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $provider = $this->providerRepository->findOneBy(['name' => Provider::SWEET_TV]);
        $films = $provider->getFilms();
        foreach ($films as $film){
            $posters = $film->getPoster();
            foreach ($posters as $poster){
                if($poster->getUploaded() == 0){
                    $uploadedFile=$this->imageFileService->getUploadFileByUrl($poster->getLink());
                    $this->imageFileService->updateUploadedStatus($poster);
                    $poster->setImageFile($uploadedFile);
                    $this->imageRepository->save($poster);
                }
            }
        }

        $output->writeln('Done!');

        return Command::SUCCESS;
    }
}