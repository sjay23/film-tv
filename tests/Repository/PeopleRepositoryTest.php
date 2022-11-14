<?php

namespace App\Tests\Repository;

use App\Entity\People;
use App\Repository\PeopleRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class PeopleRepositoryTest extends KernelTestCase
{
    protected function setUp(): void
    {
        $this->containerKernel = static::getContainer();
        $this->peopleRepository = $this->containerKernel->get(PeopleRepository::class);
    }


    public function testPopularActor()
    {
        $this->assertEquals(true, is_array($this->peopleRepository->getPopularActor()));
    }

    public function testAdd()
    {
        $people =  new People();
        $people->setName('Test_People');
        $this->peopleRepository->add($people,true);
        $this->people = $this->peopleRepository->findOneBy(['name'=>'Test_People']);

        $this->assertEquals('Test_People', $this->people->getName());
    }

    public function testRemove()
    {
        $this->people = $this->peopleRepository->findOneBy([]);
        $id = $this->people->getId();
        $this->peopleRepository->remove($this->people,true);

        $this->assertEquals(null , $this->peopleRepository->findOneBy(['id'=>$id]));
    }
}
