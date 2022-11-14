<?php

namespace App\Tests\Repository;

use App\Entity\CommandTask;
use App\Repository\CommandTaskRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class CommandTaskRepositoryTest extends KernelTestCase
{
    protected function setUp(): void
    {
        $this->containerKernel = static::getContainer();
        $this->taskRepository = $this->containerKernel->get(CommandTaskRepository::class);
    }

    public function testSave()
    {
        $task =  new CommandTask('test_tasks');
        $this->taskRepository->save($task);
        $this->task = $this->taskRepository->findOneBy(['name'=>'test_tasks']);

        $this->assertEquals('test_tasks' , $this->task->getName());
    }

    public function testAdd()
    {
        $task =  new CommandTask('test_task');
        $this->taskRepository->add($task,true);
        $this->task = $this->taskRepository->findOneBy(['name'=>'test_task']);

        $this->assertEquals('test_task'  , $this->task->getName());
    }

    public function testDelete()
    {
        $this->task = $this->taskRepository->findOneBy([]);
        $id = $this->task->getId();
        $this->taskRepository->delete($this->task);

        $this->assertEquals(null , $this->taskRepository->findOneBy(['id'=>$id]));
    }

    public function testRemove()
    {
        $this->task = $this->taskRepository->findOneBy([]);
        $id = $this->task->getId();
        $this->taskRepository->remove($this->task,true);

        $this->assertEquals(null , $this->taskRepository->findOneBy(['id'=>$id]));
    }
}
