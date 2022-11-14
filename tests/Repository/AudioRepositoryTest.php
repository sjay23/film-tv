<?php

namespace App\Tests\Repository;

use App\Entity\Audio;
use App\Repository\AudioRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AudioRepositoryTest extends KernelTestCase
{
    protected function setUp(): void
    {
        $this->containerKernel = static::getContainer();
        $this->audioRepository = $this->containerKernel->get(AudioRepository::class);
    }


    public function testSave()
    {
        $audio =  new Audio();
        $audio->setName('Test_Audio');
        $this->audioRepository->save($audio);
        $this->audio = $this->audioRepository->findOneBy(['name'=>'Test_Audio']);

        $this->assertEquals('Test_Audio' , $this->audio->getName());
    }

    public function testDelete()
    {
        $this->audio = $this->audioRepository->findOneBy([]);
        $id = $this->audio->getId();
        $this->audioRepository->delete($this->audio);

        $this->assertEquals(null , $this->audioRepository->findOneBy(['id'=>$id]));
    }
}
