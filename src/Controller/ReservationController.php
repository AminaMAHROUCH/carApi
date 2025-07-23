<?php

namespace App\Controller;

use App\Service\Contracts\ReservationServiceInterface;
use App\Service\Contracts\AccessControlServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use App\Exception\CarUnavailableException;
use App\Exception\InvalidReservationDateException;

#[Route('/api/reservations', name: 'api_reservation_')]
class ReservationController extends AbstractController
{
    public function __construct(
        private Security $security,
        private AccessControlServiceInterface $accessControl,
        private ReservationServiceInterface $reservationService
    ) {
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = $request->getPayload()->all();
        $carId = $data['carId'] ?? null;
        $startDate = $data['startDate'] ?? null;
        $endDate = $data['endDate'] ?? null;
        $user = $this->security->getUser();
        try {
            $reservation = $this->reservationService->create($user, $carId, $startDate, $endDate);
        } catch (CarUnavailableException|InvalidReservationDateException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
        return $this->json($reservation, 201, [], ['groups' => 'reservation:read']);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $data = $request->getPayload()->all();
        $startDate = $data['startDate'] ?? null;
        $endDate = $data['endDate'] ?? null;
        $user = $this->security->getUser();
        $reservation = $this->reservationService->find($id);
        if (!$reservation) {
            return $this->json(['error' => 'Reservation not found'], 404);
        }
        if (!$this->accessControl->canAccess($reservation)) {
            return $this->json(['error' => 'Unauthorized'], 403);
        }
        try {
            $reservation = $this->reservationService->update($id, $user, $startDate, $endDate);
        } catch (CarUnavailableException|InvalidReservationDateException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
        return $this->json($reservation, 200, [], ['groups' => 'reservation:read']);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $user = $this->security->getUser();
        $reservation = $this->reservationService->find($id);
        if (!$reservation) {
            return $this->json(['error' => 'Reservation not found'], 404);
        }
        if (!$this->accessControl->canAccess($reservation)) {
            return $this->json(['error' => 'Unauthorized'], 403);
        }
        $this->reservationService->delete($id, $user);
        return $this->json(null, 204);
    }

    #[Route('', name: 'all', methods: ['GET'])]
    public function all(): JsonResponse
    {
        $reservations = $this->reservationService->all();
        return $this->json($reservations, 200, [], ['groups' => 'reservation:read']);
    }
}

