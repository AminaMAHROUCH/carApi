<?php

namespace App\Service;

use App\Entity\Car;
use App\Entity\Reservation;
use App\Entity\User;
use App\Repository\ReservationRepository;
use App\Repository\CarRepository;
use App\Exception\CarUnavailableException;
use App\Exception\InvalidReservationDateException;
use App\Service\Contracts\ReservationServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

class ReservationService implements ReservationServiceInterface
{
    public function __construct(private readonly ReservationRepository $reservationRepository,private readonly CarRepository $carRepository,private readonly EntityManagerInterface $entityManager) 
    {}

    public function create(User $user, int $carId, string $startDate, string $endDate): Reservation
    {
        $car = $this->carRepository->find($carId);
        if (!$car) {
            throw new \Exception('Car not found');
        }
        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        if ($start >= $end) {
            throw new InvalidReservationDateException('Start date must be before end date.');
        }
        if (!$this->isCarAvailable($car, $start, $end)) {
            throw new CarUnavailableException('Car not available for the selected period.');
        }
        $reservation = new Reservation();
        $reservation->setUser($user);
        $reservation->setCar($car);
        $reservation->setStartDate($start);
        $reservation->setEndDate($end);
        $this->entityManager->persist($reservation);
        $this->entityManager->flush();
        return $reservation;
    }

    public function update(int $reservationId, User $user, string $startDate, string $endDate): Reservation
    {
        $reservation = $this->reservationRepository->find($reservationId);
        if (!$reservation) {
            throw new \Exception('Reservation not found');
        }
        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        if ($start >= $end) {
            throw new InvalidReservationDateException('Start date must be before end date.');
        }
        if (!$this->isCarAvailable($reservation->getCar(), $start, $end, $reservation->getId())) {
            throw new CarUnavailableException('Car not available for the selected period.');
        }
        $reservation->setStartDate($start);
        $reservation->setEndDate($end);
        $this->entityManager->flush();
        return $reservation;
    }

    public function delete(int $reservationId, User $user): void
    {
        $reservation = $this->reservationRepository->find($reservationId);
        if (!$reservation) {
            throw new \Exception('Reservation not found');
        }
        $this->entityManager->remove($reservation);
        $this->entityManager->flush();
    }

    public function all(): array
    {
        return $this->reservationRepository->findAll();
    }

    public function allByUser(User $user): array
    {
        return $this->reservationRepository->findBy(['user' => $user]);
    }

    public function find(int $id): ?Reservation
    {
        return $this->reservationRepository->find($id);
    }

    public function isCarAvailable(Car $car, \DateTimeInterface $startDate, \DateTimeInterface $endDate, ?int $excludeId = null): bool
    {
        $overlapping = $this->reservationRepository->findOverlappingReservations($car, $startDate, $endDate, $excludeId);
        return count($overlapping) === 0;
    }
}
