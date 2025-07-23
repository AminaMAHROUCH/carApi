<?php

namespace App\Service;

use App\Entity\Reservation;
use App\Service\Contracts\AccessControlServiceInterface;
use Symfony\Component\Security\Core\Security;

class AccessControlService implements AccessControlServiceInterface
{
    public function __construct(private readonly Security $security)
    {}

    public function canAccess(Reservation $reservation): bool
    {
        $currentUser = $this->security->getUser();
        return $currentUser && $currentUser->getId() === $reservation->getUser()->getId();
    }

    public function canAccessUserId($currentUser, int $userId): bool
    {
        return $currentUser && $currentUser->getId() === $userId;
    }
}
