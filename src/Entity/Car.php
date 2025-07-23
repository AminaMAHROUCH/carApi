<?php

namespace App\Entity;

use App\Repository\CarRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CarRepository::class)]
class Car
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['car:read', 'reservation:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(['car:read', 'reservation:read'])]
    #[Assert\Type('string')]
    private string $brand;

    #[ORM\Column(length: 100)]
    #[Groups(['car:read', 'reservation:read'])]
    #[Assert\Type('string')]
    private string $model;

    #[ORM\Column(length: 50, unique: true)]
    #[Groups(['car:read'])]
    #[Assert\Type('string')]
    private string $registrationNumber;

    #[ORM\OneToMany(mappedBy: 'car', targetEntity: Reservation::class)]

    public function __construct() {}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrand(): string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): static
    {
        $this->brand = $brand;
        
        return $this;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getRegistrationNumber(): string
    {
        return $this->registrationNumber;
    }

    public function setRegistrationNumber(string $registrationNumber): static
    {
        $this->registrationNumber = $registrationNumber;

        return $this;
    }
}

