<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($i = 0; $i < 20; $i++) {
            $user = new User();

            $user->setFirstName($faker->firstName);
            $user->setName($faker->name);
            $user->setMail($faker->email);
            $user->setPassword($faker->password);
            $user->setUsername($faker->userName);
            $user->setPhone($faker->phoneNumber);
            $user->setSite();
            $user->setIsActif($faker->isAdmin);
            $user->setIsAdmin($faker->isActif);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
