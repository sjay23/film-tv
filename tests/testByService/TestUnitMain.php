<?php

namespace App\Tests\testByService;

use App\Service\Parsers\MainParserService;
use App\Service\Parsers\Megogo\FilmFieldService;
use App\Service\Parsers\MegogoService;
use App\Service\Parsers\SweetTvService;
use App\Utility\CrawlerTrait;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use ReflectionClass;
use ReflectionException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TestUnitMain extends KernelTestCase
{
    protected function setUp(): void
    {
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->clientParser = new Client();
        $this->traitClass = $this->getObjectForTrait(CrawlerTrait::class);
        $this->containerKernel = static::getContainer();
        $this->sweetTvService = $this->containerKernel->get(SweetTvService::class);
        $this->megogoService = $this->containerKernel->get(MegogoService::class);
    }

    /**
     * @throws ReflectionException
     */
    public function getCookies(MainParserService $object, string $lang)
    {
        return $this->invokeMethod(
            $object,
            'getCookies',
            [$lang]
        );
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     * @throws ReflectionException
     */
    public function invokeMethod(object &$object, string $methodName, array $parameters = []): mixed
    {
        $reflection = new ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
