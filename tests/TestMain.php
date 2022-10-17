<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase as SymfonyApiTestCase;


class TestMain extends SymfonyApiTestCase
{
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->userToken = $this->createJwtToken('test@jelvix.com', 'ROLE_SUPER_ADMIN');
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

    public function getCollection($url, $entity): void
    {
        $this->sendGetUri($url);
        $this->assertMatchesResourceCollectionJsonSchema($entity);
    }

    public function getRecord($entity): void
    {
        $iri = static::findIriBy($entity, []);
        if ($iri) {
            $this->sendGetUri($iri);
        }
        $this->assertMatchesResourceItemJsonSchema($entity);
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

}