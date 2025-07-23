<?php

namespace App\Service\Contracts;

use App\Entity\Car;

interface CarServiceInterface
{
    public function all(): array;

    public function getById(int $id): ?Car;
} 