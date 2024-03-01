<?php

namespace App\DataFixtures;

use App\Entity\Accaunt;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AccauntFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $accauntTwo = new Accaunt();
        $accauntTwo->setTitle('Счет №1');
        $accauntTwo->setBrockerTitle('239900****CG');
        $manager->persist($accauntTwo);

        $accauntTwo = new Accaunt();
        $accauntTwo->setTitle('Счет №2');
        $accauntTwo->setBrockerTitle('240000****CG');
        $manager->persist($accauntTwo);

        $manager->flush();
    }
}
