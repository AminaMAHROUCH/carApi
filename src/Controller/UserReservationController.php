<?php

namespace App\Controller;

use App\Service\Contracts\ReservationServiceInterface;
use App\Service\Contracts\AccessControlServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/api/users', name: 'api_user_')]
class UserReservationController extends AbstractController
{
    public function __construct(
        private Security $security,
        private ReservationServiceInterface $reservationService,
        private AccessControlServiceInterface $accessControl
    ) {
    }

    #[Route('/{id}/reservations', name: 'reservations', methods: ['GET'])]
    public function userReservations(int $id): JsonResponse
    {
        $currentUser = $this->security->getUser();
        if (!$this->accessControl->canAccessUserId($currentUser, $id)) {
            return $this->json(['error' => 'Access denied'], 403);
        }
        $reservations = $this->reservationService->allByUser($currentUser);
        return $this->json($reservations, 200, [], ['groups' => 'reservation:read']);
    }
}