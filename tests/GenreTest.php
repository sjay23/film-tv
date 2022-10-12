<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase as SymfonyApiTestCase;
use App\Entity\Genre;


class GenreTest extends SymfonyApiTestCase
{


    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->userToken = $this->createJwtToken('test@jelvix.com', 'ROLE_SUPER_ADMIN');
    }

    public function testGetCollection(): void
    {
        $response = static::createClient()->request('GET', '/api/genres', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => '*/*'
            ]]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');
        $this->assertMatchesResourceCollectionJsonSchema(Genre::class);
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