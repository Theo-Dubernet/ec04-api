<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProduitControllerTest extends WebTestCase
{
    public function testCreateProduit(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/produit',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'nom' => 'Pizza',
                'prix' => 12.50
            ])
        );

        $this->assertResponseStatusCodeSame(201);

        $this->assertJson($client->getResponse()->getContent());

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('id', $data);
        $this->assertEquals('Pizza', $data['nom']);
    }
}