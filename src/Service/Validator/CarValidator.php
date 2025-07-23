<?php

namespace App\Service\Validator;

use App\Repository\CarRepository;
use App\Exception\DuplicateCarRegistrationException;

class CarValidator
{
    private CarRepository $carRepository;

    public function __construct(CarRepository $carRepository)
    {
        $this->carRepository = $carRepository;
    }

    public function validateUniqueRegistration(string $registrationNumber): void
    {
        if ($this->carRepository->findOneBy(['registrationNumber' => $registrationNumber])) {
            throw new DuplicateCarRegistrationException('Car registration number must be unique.');
        }
    }
} 