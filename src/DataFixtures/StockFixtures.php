<?php

namespace App\DataFixtures;

use App\Entity\Stock;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StockFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $sber = new Stock();
        $sber->setTitle('Сбербанк России ПАО ао');
        $sber->setSecId('SBER');
        $sber->setPrice(284.77);
        $sber->setLotSize(10);
        $sber->setMinStep(0.01);
        $manager->persist($sber);

        $gazp = new Stock();
        $gazp->setTitle('"Газпром" (ПАО) ао');
        $gazp->setSecId('GAZP');
        $gazp->setPrice(158.12);
        $gazp->setLotSize(10);
        $gazp->setMinStep(0.01);
        $manager->persist($gazp);

        $manager->flush();
    }
}
