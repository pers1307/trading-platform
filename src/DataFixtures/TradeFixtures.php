<?php

namespace App\DataFixtures;

use App\Entity\Trade;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TradeFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            AccauntFixtures::class,
            StrategyFixtures::class,
            StockFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $trade = new Trade();

        $trade->setStock($this->getReference(StockFixtures::GAZP));
        $trade->setAccaunt($this->getReference(AccauntFixtures::ACCAUNT_ONE));
        $trade->setStrategy($this->getReference(StrategyFixtures::MY_STRATEGY));
        $trade->setType('long');
        $trade->setOpenDateTime(new \DateTime('2024-03-01 08:05:19'));
        $trade->setOpenPrice(150.00);
        $trade->setStopLoss(100);
        $trade->setTakeProfit(200);
        $trade->setLots(1);
        $trade->setStatus('open');
        $trade->setDescription('');

        $manager->persist($trade);

        $trade2 = new Trade();
        $trade2->setStock($this->getReference(StockFixtures::SBER));
        $trade2->setAccaunt($this->getReference(AccauntFixtures::ACCAUNT_ONE));
        $trade2->setStrategy($this->getReference(StrategyFixtures::MY_STRATEGY));
        $trade2->setType('short');
        $trade2->setOpenDateTime(new \DateTime('2024-03-05 10:05:19'));
        $trade2->setOpenPrice(300.00);
        $trade2->setStopLoss(350);
        $trade2->setTakeProfit(250);
        $trade2->setLots(1);
        $trade2->setStatus('open');
        $trade2->setDescription('');

        $manager->persist($trade2);

        $manager->flush();
    }
}
