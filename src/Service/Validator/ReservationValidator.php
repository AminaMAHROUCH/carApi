<?php

namespace App\Service\Validator;

use App\Entity\Car;
use App\Exception\InvalidReservationDateException;
use App\Exception\CarUnavailableException;
use App\Repository\ReservationRepository;

class ReservationValidator
{
    private ReservationRepository $reservationRepository;

    public function __construct(ReservationRepository $reservationRepository)
    {
        $this->reservationRepository = $reservationRepository;
    }

    public function validateDates(\DateTimeInterface $startDate, \DateTimeInterface $endDate): void
    {
        if ($startDate >= $endDate) {
            throw new InvalidReservationDateException('Start date must be before end date.');
        }
    }

    public function validateCarAvailability(Car $car, \DateTimeInterface $startDate, \DateTimeInterface $endDate, ?int $excludeId = null): void
    {
        $overlapping = $this->reservationRepository->findOverlappingReservations($car, $startDate, $endDate, $excludeId);
        if (count($overlapping) > 0) {
            throw new CarUnavailableException('Car not available for the selected period.');
        }
    }
} 