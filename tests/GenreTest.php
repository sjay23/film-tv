<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase as SymfonyApiTestCase;
use App\Entity\Genre;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class GenreTest extends SymfonyApiTestCase
{
    //use RefreshDatabaseTrait;

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
}
