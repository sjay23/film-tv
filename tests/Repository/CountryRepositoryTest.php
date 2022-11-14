<?php

namespace App\Tests\Repository;

use App\Entity\Country;
use App\Repository\CountryRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class CountryRepositoryTest extends KernelTestCase
{
    protected function setUp(): void
    {
        $this->containerKernel = static::getContainer();
        $this->countryRepository = $this->containerKernel->get(CountryRepository::class);
    }

    public function testAdd()
    {
        $country =  new Country();
        $country->setName('Test_Country');
        $this->countryRepository->add($country , true);
        $this->country = $this->countryRepository->findOneBy(['name'=>'Test_Country']);
        $this->assertEquals('Test_Country', $this->country->getName());
    }

    public function testRemove()
    {
        $country = $this->countryRepository->findOneBy([]);
        $id = $country->getId();
        $this->countryRepository->remove($country , true);

        $this->assertEquals(null , $this->countryRepository->findOneBy(['id'=>$id]));
    }
}
