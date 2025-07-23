<?php

namespace App\Tests\Feature\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserReservationControllerTest extends WebTestCase
{
    private function getAuthenticatedClient(): \Symfony\Bundle\FrameworkBundle\KernelBrowser
    {
        $client = static::createClient();
        $client->request('POST', '/api/login_check', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => 'amina@gmail.com',
            'password' => 'password23',
        ]));
        $data = json_decode($client->getResponse()->getContent(), true);
        $client->setServerParameter('HTTP_AUTHORIZATION', 'Bearer ' . $data['token']);
        return $client;
    }

    public function testUserCanAccessOwnReservations(): void
    {
        $client = $this->getAuthenticatedClient();
        $client->request('GET', '/api/users/1/reservations');
        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($data);
    }

    public function testUserCannotAccessOthersReservations(): void
    {
        $client = $this->getAuthenticatedClient();
        $client->request('GET', '/api/users/9999/reservations');
        $this->assertResponseStatusCodeSame(403);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
        $this->assertEquals('Access denied', $data['error']);
    }
} 