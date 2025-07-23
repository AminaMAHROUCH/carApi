<?php

namespace App\Tests\Unit\Service;

use App\Entity\Car;
use App\Repository\CarRepository;
use App\Service\CarService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class CarServiceTest extends TestCase
{
    private $carRepository;
    private $entityManager;
    private $service;

    protected function setUp(): void
    {
        $this->carRepository = $this->createMock(CarRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->service = new CarService(
            $this->carRepository,
            $this->entityManager
        );
    }

    public function testAllReturnsAllCars(): void
    {
        $cars = [new Car(), new Car()];
        $this->carRepository->method('findAll')->willReturn($cars);
        $result = $this->service->all();
        $this->assertSame($cars, $result);
    }

    public function testGetByIdReturnsCar(): void
    {
        $car = new Car();
        $this->carRepository->method('find')->with(1)->willReturn($car);
        $result = $this->service->getById(1);
        $this->assertSame($car, $result);
    }

    public function testGetByIdReturnsNullIfNotFound(): void
    {
        $this->carRepository->method('find')->with(999)->willReturn(null);
        $result = $this->service->getById(999);
        $this->assertNull($result);
    }
} 