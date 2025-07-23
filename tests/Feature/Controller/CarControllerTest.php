<?php

namespace App\Tests\Feature\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CarControllerTest extends WebTestCase
{
    private function getAuthenticatedClient(): \Symfony\Bundle\FrameworkBundle\KernelBrowser
    {
        $client = static::createClient();
        $client->request('POST', '/api/login_check', [], [], [
            'CONTENT_TYPE' => 'application/json'
        ], json_encode([
            'email' => 'amina@gmail.com',
            'password' => 'password23'
        ]));
        $data = json_decode($client->getResponse()->getContent(), true);
        $client->setServerParameter('HTTP_AUTHORIZATION', 'Bearer ' . $data['token']);
        return $client;
    }

    public function testListCars(): void
    {
        $client = $this->getAuthenticatedClient();
        $client->request('GET', '/api/cars');
        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($data);
    }

    public function testShowCarDetails(): void
    {
        $client = $this->getAuthenticatedClient();
        $client->request('GET', '/api/cars/1');
        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertEquals(1, $data['id']);
    }

    public function testShowCarNotFound(): void
    {
        $client = $this->getAuthenticatedClient();
        $client->request('GET', '/api/cars/9999');
        $this->assertResponseStatusCodeSame(404);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
        $this->assertEquals('Car not found', $data['error']);
    }
} 