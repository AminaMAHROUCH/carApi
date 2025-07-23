<?php

namespace App\Service\Contracts;

use App\Entity\User;
use App\Entity\Car;
use App\Entity\Reservation;

interface ReservationServiceInterface
{
    public function create(User $user, int $carId, string $startDate, string $endDate): Reservation;
    public function update(int $reservationId, User $user, string $startDate, string $endDate): Reservation;
    public function delete(int $reservationId, User $user): void;
    public function all(): array;
    public function allByUser(User $user): array;
    public function find(int $id): ?Reservation;
    public function isCarAvailable(Car $car, \DateTimeInterface $startDate, \DateTimeInterface $endDate, ?int $excludeId = null): bool;
} 