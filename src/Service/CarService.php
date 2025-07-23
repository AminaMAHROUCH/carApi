<?php

namespace App\Service;

use App\Entity\Car;
use App\Repository\CarRepository;
use App\Service\Contracts\CarServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

class CarService implements CarServiceInterface
{
    public function __construct(private readonly CarRepository $carRepository,private readonly EntityManagerInterface $entityManager) 
    {}

    public function all(): array
    {
        return $this->carRepository->findAll();
    }

    public function getById(int $id): ?Car
    {
        return $this->carRepository->find($id);
    }
} 