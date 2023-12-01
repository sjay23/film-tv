<?php

namespace App\Controller;

use App\Entity\Provider;
use App\Repository\ProviderRepository;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class ProviderController
{
    /**
     * @var ProviderRepository
     */
    private ProviderRepository $providerRepository;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @param ProviderRepository $providerRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        ProviderRepository $providerRepository,
        EntityManagerInterface $entityManager,
    ) {
        $this->providerRepository = $providerRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param Request $request
     * @return Provider
     * @throws Exception
     */
    public function addProvider(Request $request): Provider
    {

        if ($provider = $this->providerRepository->findOneBy(['name' => $request->get('name')])) {
            throw new Exception('The provider already exists');
        } else {
            $provider = new Provider($request->get('name'));
            $this->entityManager->persist($provider);
            $this->entityManager->flush();
        }
        return $provider;
    }

    /**
     * @param Request $request
     * @return Provider
     * @throws Exception
     */
    public function updateProvider(Request $request, Provider $provider): Provider
    {
        $provider->setName($request->get('name'));
        $this->entityManager->flush();

        return $provider;
    }

    public function deleteProvider(Provider $provider): Provider
    {
        $this->entityManager->remove($provider);
        $this->entityManager->flush();

        return $provider;
    }
}
