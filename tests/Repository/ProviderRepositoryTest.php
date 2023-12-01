<?php

namespace App\Tests\Repository;

use App\Entity\Provider;
use App\Repository\ProviderRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class ProviderRepositoryTest extends KernelTestCase
{
    protected function setUp(): void
    {
        $this->containerKernel = static::getContainer();
        $this->providerRepository = $this->containerKernel->get(ProviderRepository::class);
    }

    public function testSave()
    {
        $provider =  new Provider('Test_Provider');
        $this->providerRepository->save($provider);
        $this->providerRepository = $this->providerRepository->findOneBy(['name'=>'Test_Provider']);

        $this->assertEquals('Test_Provider', $this->providerRepository->getName());
    }

    public function testDelete()
    {
        $this->provider = $this->providerRepository->findOneBy([]);
        $id = $this->provider->getId();
        $this->providerRepository->delete($this->provider);

        $this->assertEquals(null , $this->providerRepository->findOneBy(['id'=>$id]));
    }
}
