<?php

namespace App\Tests\Controller;

use App\Controller\SecurityController;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SecurityControllerTest extends KernelTestCase
{
    protected function setUp(): void
    {
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->authenticationUtils = $this->createMock(AuthenticationUtils::class);
        $this->clientParser = new Client();
        $this->containerKernel = static::getContainer();
        $this->securityController = $this->containerKernel->get(SecurityController::class);
    }

    public function testLogin()
    {
        $response = $this->securityController->login($this->authenticationUtils);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals("200", $response->getStatusCode());
    }

    public function testLogout()
    {
        $this->expectException(\LogicException::class);
        $this->securityController->logout();
    }
}
