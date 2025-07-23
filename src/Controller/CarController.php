<?php

namespace App\Controller;

use App\Service\Contracts\CarServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/cars', name: 'api_cars_')]
class CarController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(CarServiceInterface $carService): JsonResponse
    {
        $cars = $carService->all();
        return $this->json($cars, 200, [], ['groups' => 'car:read']);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id, CarServiceInterface $carService): JsonResponse
    {
        $car = $carService->getById($id);
        if (!$car) {
            return $this->json(['error' => 'Car not found'], 404);
        }
        return $this->json($car, 200, [], ['groups' => 'car:read']);
    }
}

