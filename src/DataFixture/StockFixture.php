<?php

namespace App\DataFixture;

use App\Entity\Stock;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StockFixture extends Fixture
{
    public const SBER = 'SBER';
    public const GAZP = 'GAZP';

    public static function getSber(): Stock
    {
        return (new Stock())
            ->setTitle('Сбербанк России ПАО ао')
            ->setSecId('SBER')
            ->setPrice(284.77)
            ->setLotSize(10)
            ->setMinStep(0.01);
    }

    public static function getGazp(): Stock
    {
        return (new Stock())
            ->setTitle('"Газпром" (ПАО) ао')
            ->setSecId('GAZP')
            ->setPrice(158.12)
            ->setLotSize(10)
            ->setMinStep(0.01);
    }

    public function load(ObjectManager $manager): void
    {
        $sber = self::getSber();
        $manager->persist($sber);

        $gazp = self::getGazp();
        $manager->persist($gazp);

        $manager->flush();

        $this->addReference(self::SBER, $sber);
        $this->addReference(self::GAZP, $gazp);
    }
}