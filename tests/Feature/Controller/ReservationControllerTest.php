<?php

namespace App\Tests\Feature\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ReservationControllerTest extends WebTestCase
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

    public function testCreateReservation(): void
    {
        $client = $this->getAuthenticatedClient();
        $client->request('POST', '/api/reservations', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'startDate' => '2025-07-14',
            'endDate'   => '2025-07-16',
            'carId'     => 1,
        ]));
        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
    }

    public function testCreateReservationInvalidDate(): void
    {
        $client = $this->getAuthenticatedClient();
        $client->request('POST', '/api/reservations', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'startDate' => '2025-07-20',
            'endDate'   => '2025-07-18',
            'carId'     => 1,
        ]));
        $this->assertResponseStatusCodeSame(400);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
        $this->assertStringContainsString('Start date must be before end date', $data['error']);
    }

    public function testUpdateReservation(): void
    {
        $client = $this->getAuthenticatedClient();
        $client->request('POST', '/api/reservations', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'startDate' => '2025-07-18',
            'endDate'   => '2025-07-20',
            'carId'     => 1,
        ]));
        $data = json_decode($client->getResponse()->getContent(), true);
        $reservationId = $data['id'];
        $client->request('PUT', '/api/reservations/' . $reservationId, [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'startDate' => '2025-07-19',
            'endDate'   => '2025-07-21',
        ]));
        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals($reservationId, $data['id']);
        $this->assertEquals('2025-07-19T00:00:00+00:00', $data['startDate']);
        $this->assertEquals('2025-07-21T00:00:00+00:00', $data['endDate']);
    }

    public function testDeleteReservation(): void
    {
        $client = $this->getAuthenticatedClient();
        $client->request('POST', '/api/reservations', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'startDate' => '2025-07-22',
            'endDate'   => '2025-07-24',
            'carId'     => 1,
        ]));
        $data = json_decode($client->getResponse()->getContent(), true);
        $reservationId = $data['id'];
        $client->request('DELETE', '/api/reservations/' . $reservationId);
        $this->assertResponseStatusCodeSame(204);
    }

    public function testDeleteReservationNotFound(): void
    {
        $client = $this->getAuthenticatedClient();
        $client->request('DELETE', '/api/reservations/99999');
        $this->assertResponseStatusCodeSame(404);
    }

    public function testUpdateReservationUnauthorized(): void
    {
        $client = $this->getAuthenticatedClient();
        $client->request('PUT', '/api/reservations/99999', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'startDate' => '2025-07-19',
            'endDate'   => '2025-07-21',
        ]));
        $this->assertResponseStatusCodeSame(404);
    }
}
