<?php

namespace App\Command\Cron;

use App\Entity\Provider;
use App\Repository\ProviderRepository;
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
     * @var EntityManagerInterface
     */
    private  EntityManagerInterface $entityManager;


    public function __construct( ImageFileService $imageFileService,ProviderRepository $providerRepository,EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->imageFileService = $imageFileService;
        $this->entityManager = $entityManager;
        $this->providerRepository = $providerRepository;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $provider = $this->providerRepository->findOneBy(['name' => Provider::SWEET_TV]);
        $films=$provider->getFilms();
        foreach ($films as $film){
            $posters= $film->getPoster();
            foreach ($posters as $poster){
                if($poster->getUploaded() == 0){
                    $uploadedFile=$this->imageFileService->getUploadFileByUrl($poster);
                    $poster->setImageFile($uploadedFile);
                }
            }

        }
        $this->entityManager->flush();
        $output->writeln('Done!');

        return Command::SUCCESS;
    }
}