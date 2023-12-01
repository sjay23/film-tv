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

    public function getCollection($url ,$entity): void
    {
        $response = static::createClient()->request('GET', $url, [
            'headers' => [
                'Authorization' => 'Bearer ' .  $this->userToken,
                'Content-Type' => 'application/json',
                'Accept' => '*/*'
            ]]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');
        $this->assertMatchesResourceCollectionJsonSchema($entity);
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