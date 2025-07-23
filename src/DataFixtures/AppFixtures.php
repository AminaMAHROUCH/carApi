<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Car;
use App\Entity\Reservation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('amina@gmail.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->hasher->hashPassword($user, 'password23'));
        $manager->persist($user);

        $cars = [];
        for ($i = 1; $i <= 4; $i++) {
            $car = new Car();
            $car->setBrand('Brand ' . $i);
            $car->setModel('Model ' . $i);
            $car->setRegistrationNumber('ABC-00' . $i);
            $manager->persist($car);
            $cars[] = $car;
        }

        $manager->flush();
    }
}
