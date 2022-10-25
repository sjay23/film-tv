<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase as SymfonyApiTestCase;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;


class TestMain extends SymfonyApiTestCase
{
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->userToken = $this->createJwtToken('test@jelvix.com', 'ROLE_SUPER_ADMIN');

        $kernel = self::bootKernel();
        //DatabasePrimer::prime($kernel);
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->router = $kernel->getContainer()->get('router');
    }

    public function sendGetUri($url)
    {
        $response = static::createClient()->request('GET', $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->userToken,
                'Content-Type' => 'application/json',
                'Accept' => '*/*'
            ]]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');
        return $response;
    }

    /**
     * @param string $iri
     * @param array $data
     * @return ResponseInterface
     * @throws TransportExceptionInterface
     */
    protected function sendPostUri(string $iri, array $data = []): ResponseInterface
    {
        $extra = [];
        if (!empty($data)) {
            $extra['parameters'] = $data;
        }

        $response = $this->client->request('POST', $iri, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->userToken,
                'Content-Type' => 'application/json',
                'Accept' => '*/*'
            ],
            'extra' => $extra
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');
        return $response;
    }

    /**
     * @param string $iri
     * @param array $data
     * @return ResponseInterface
     * @throws TransportExceptionInterface
     */
    protected function sendPostUriForUpdate(string $iri, array $data = []): ResponseInterface
    {
        $extra = [];
        if (!empty($data)) {
            $extra['parameters'] = $data;
        }

        $response = $this->client->request('PATCH', $iri, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->userToken,
                'Content-Type' => 'application/json',
                'Accept' => '*/*'
            ],
            'extra' => $extra
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');
        return $response;
    }

    /**
     * @param string $iri
     * @param array $data
     * @return ResponseInterface
     * @throws TransportExceptionInterface
     */
    protected function sendPostUriForUploadFile(string $iri, array $data = []): ResponseInterface
    {
        $extra = [];
        if (!empty($data)) {
            $extra['parameters'] = $data;
        }
        if (!empty($files)) {
            $extra['files'] = $files;
        }
        $response = $this->client->request('POST', $iri, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->userToken,
                'Content-Type' => 'application/json',
                'Accept' => '*/*'
            ],
            'extra' => $extra
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');
        return $response;
    }

    public function getCollection($url, $entity): void
    {
        $this->sendGetUri($url);
        $this->assertMatchesResourceCollectionJsonSchema($entity);
    }

    public function getRecord($entity): void
    {
        $iri = $this->getIri($entity);
        if ($iri) {
            $this->sendGetUri($iri);
        }
        $this->assertMatchesResourceItemJsonSchema($entity);
    }

    public function getIri($entity)
    {
        return static::findIriBy($entity, []);
    }

    /**
     * @param string $username
     * @param string $role
     * @return string
     */
    protected function createJwtToken(string $username, string $role): string
    {
        $data = array('username' => $username, 'roles' => [$role]);
        return $this->client->getContainer()
            ->get('lexik_jwt_authentication.encoder')
            ->encode($data);
    }

    /**
     * @param string $uri
     * @param array $data
     * @return void
     * @throws TransportExceptionInterface
     */
    protected function sendDeleteUri(string $uri, array $data = [])
    {
        $this->client->request('DELETE', $uri, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->userToken,
                'Content-Type' => 'application/json',
                'Accept' => '*/*'
            ],
            'extra' => [
                'parameters' => $data
            ]
        ]);

        $this->assertResponseIsSuccessful();
    }

}