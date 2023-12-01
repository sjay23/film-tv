<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class UserRepositoryTest extends KernelTestCase
{
    protected function setUp(): void
    {
        $this->containerKernel = static::getContainer();
        $this->userRepository = $this->containerKernel->get(UserRepository::class);
    }


    public function testAdd()
    {
        $user =  new User('testTestovich_email','1234567',["ROLE_SUPER_ADMIN"]);
        $this->userRepository->add($user,true);
        $this->user = $this->userRepository->findOneBy(['email'=>'testTestovich_email']);

        $this->assertEquals('testTestovich_email', $this->user->getEmail());
    }

    public function testRemove()
    {
        $this->user = $this->userRepository->findOneBy([]);
        $id = $this->user->getId();
        $this->userRepository->remove($this->user,true);

        $this->assertEquals(null , $this->userRepository->findOneBy(['id'=>$id]));
    }
}
