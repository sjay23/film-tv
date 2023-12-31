<?php

namespace App\Controller;

use App\DTO\AudioInput;
use App\Entity\Audio;
use App\Repository\AudioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 *
 */
class AudioController
{
    /**
     * @var ValidatorInterface
     */
    private ValidatorInterface $validator;

    /**
     * @var AudioRepository
     */
    private AudioRepository $audioRepository;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @param ValidatorInterface $validator
     * @param AudioRepository $audioRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        AudioRepository $audioRepository,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
    ) {
        $this->audioRepository = $audioRepository;
        $this->validator = $validator;
        $this->entityManager = $entityManager;
    }

    /**
     * @param Request $request
     * @return Audio
     * @throws Exception
     */
    public function addAudio(Request $request): Audio
    {
        $audioInput = new AudioInput(
            $request->get('name')
        );
        $this->validator->validate($audioInput);

        if ($audio = $this->audioRepository->findOneBy(['name' => $audioInput->getName()])) {
            throw new Exception('The audio already exists');
        } else {
            $audio = new Audio();
            $audio->setName($audioInput->getName());
            $this->entityManager->persist($audio);
            $this->entityManager->flush();
        }
        return $audio;
    }

    /**
     * @param Request $request
     * @return Audio
     * @throws Exception
     */
    public function updateAudio(Request $request, Audio $audio): Audio
    {
            $audio->setName($request->get('name'));
            $this->entityManager->flush();

        return $audio;
    }

    public function deleteAudio(Audio $audio): Audio
    {
        $this->entityManager->remove($audio);
        $this->entityManager->flush();

        return $audio;
    }
}
