<?php

namespace App\Service;

use App\Entity\Audio;
use App\Repository\AudioRepository;
use Doctrine\ORM\EntityManagerInterface;

class AudioService
{
    private EntityManagerInterface $entityManager;
    private AudioRepository $audioRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        AudioRepository $audioRepository

    ) {
        $this->entityManager = $entityManager;
        $this->audioRepository = $audioRepository;

    }

    public function getAudio($audioInput)
    {
        if ($this->audioRepository->findOneBy(['name' => $audioInput->getName()])) {
            $audio = $this->audioRepository->findOneBy(['name' => $audioInput->getName()]);
            return $audio;
        } else {
            $audio = new Audio();
            $audio->setName($audioInput->getName());
            $this->entityManager->persist($audio);
            return $audio;
        }

    }
}