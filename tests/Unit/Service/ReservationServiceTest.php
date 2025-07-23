<?php

namespace App\Tests\Unit\Service;

use App\Entity\Car;
use App\Entity\User;
use App\Entity\Reservation;
use App\Exception\CarUnavailableException;
use App\Exception\InvalidReservationDateException;
use App\Repository\ReservationRepository;
use App\Repository\CarRepository;
use App\Service\ReservationService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class ReservationServiceTest extends TestCase
{
    private $reservationRepository;
    private $carRepository;
    private $entityManager;
    private $service;

    protected function setUp(): void
    {
        $this->reservationRepository = $this->createMock(ReservationRepository::class);
        $this->carRepository = $this->createMock(CarRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->service = new ReservationService(
            $this->reservationRepository,
            $this->carRepository,
            $this->entityManager
        );
    }

    public function testSaveThrowsOnInvalidDates(): void
    {
        $user = $this->createMock(User::class);
        $car = $this->createMock(Car::class);
        $start = new \DateTime('2025-07-20');
        $end = new \DateTime('2025-07-18');
        $this->carRepository->method('find')->willReturn($car);
        $this->expectException(InvalidReservationDateException::class);
        $this->service->create($user, 1, $start->format('Y-m-d'), $end->format('Y-m-d'));
    }

    public function testSaveThrowsOnCarUnavailable(): void
    {
        $user = $this->createMock(User::class);
        $car = $this->createMock(Car::class);
        $start = new \DateTime('2025-07-18');
        $end = new \DateTime('2025-07-20');
        $this->carRepository->method('find')->willReturn($car);
        $this->reservationRepository->method('findOverlappingReservations')->willReturn([new Reservation()]);
        $this->expectException(CarUnavailableException::class);
        $this->service->create($user, 1, $start->format('Y-m-d'), $end->format('Y-m-d'));
    }

    public function testSavePersistsReservation(): void
    {
        $user = $this->createMock(User::class);
        $car = $this->createMock(Car::class);
        $start = new \DateTime('2025-07-18');
        $end = new \DateTime('2025-07-20');
        $this->carRepository->method('find')->willReturn($car);
        $this->reservationRepository->method('findOverlappingReservations')->willReturn([]);
        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->once())->method('flush');
        $reservation = $this->service->create($user, 1, $start->format('Y-m-d'), $end->format('Y-m-d'));
        $this->assertInstanceOf(Reservation::class, $reservation);
        $this->assertSame($user, $reservation->getUser());
        $this->assertSame($car, $reservation->getCar());
        $this->assertEquals($start, $reservation->getStartDate());
        $this->assertEquals($end, $reservation->getEndDate());
    }
} 