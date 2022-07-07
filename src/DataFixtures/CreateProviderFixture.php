<?php

namespace App\DataFixtures;

use App\Entity\Provider;
use App\Repository\ProviderRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CreateProviderFixture extends Fixture
{

    private ProviderRepository $providerRepository;

    /**
     * run console - php bin/console doctrine:fixtures:load --append --group=CreateProviderFixture
     */

    public function __construct(
        ProviderRepository $providerRepository
    )
    {
        $this->providerRepository = $providerRepository;
    }

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $provider = new Provider(
            'SweetTv'
        );

        try {
            $this->providerRepository->save($provider);
            $manager->persist($provider);
        } catch (\Exception $exception) {
            dd($exception->getMessage());
        }
    }
}
