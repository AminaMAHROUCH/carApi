<?php

namespace App\Service\Contracts;

use App\Entity\Reservation;

interface AccessControlServiceInterface
{
    public function canAccess(Reservation $reservation): bool;
    public function canAccessUserId($currentUser, int $userId): bool;
} 