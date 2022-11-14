<?php

namespace App\Tests\Controller;

use App\Controller\MainParserController;
use App\Repository\CommandTaskRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Response;


class MainParserControllerTest extends KernelTestCase
{
    protected function setUp(): void
    {
        $this->commandTaskRepository = $this->createMock(CommandTaskRepository::class);
        $this->task = $this->commandTaskRepository->findOneBy(['provider' => 1]);
        $this->containerKernel = static::getContainer();
        $this->mainController = $this->containerKernel->get(MainParserController::class);
    }

    public function testIndex()
    {
        $response = $this->mainController->index();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals("200", $response->getStatusCode());
    }

    public function testSweetTvParse()
    {
        $response = $this->mainController->sweetTvParse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals("302", $response->getStatusCode());
    }

    public function testSweetTvParseStop()
    {
        $response = $this->mainController->sweetTvParseStop();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals("302", $response->getStatusCode());
    }
}
