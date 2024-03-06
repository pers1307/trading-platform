<?php

namespace App\DataFixture;

use App\Entity\Trade;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TradeFixture extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            AccauntFixture::class,
            StrategyFixture::class,
            StockFixture::class,
        ];
    }

    public static function getLongTrade(): Trade
    {
        $trade = new Trade();
        $trade->setType(Trade::TYPE_LONG);
        $trade->setOpenDateTime(new \DateTime('2024-03-01 08:05:19'));
        $trade->setOpenPrice(200.0);
        $trade->setCloseDateTime(new \DateTime('2024-03-03 10:00:00'));
        $trade->setClosePrice(250.0);
        $trade->setStopLoss(150);
        $trade->setTakeProfit(250);
        $trade->setLots(1);
        $trade->setStatus(Trade::STATUS_CLOSE);
        $trade->setDescription('');

        return $trade;
    }

    public static function getShortTrade(): Trade
    {
        $trade = new Trade();
        //        $trade->setStock(StockFixture::getSber());
        //        $trade->setAccaunt(AccauntFixture::getOneAccaunt());
        //        $trade->setStrategy(StrategyFixture::getMyStrategy());
        $trade->setType(Trade::TYPE_SHORT);
        $trade->setOpenDateTime(new \DateTime('2024-03-01 10:05:19'));
        $trade->setOpenPrice(200.00);
        $trade->setCloseDateTime(new \DateTime('2024-03-05 10:05:19'));
        $trade->setClosePrice(220.00);
        $trade->setStopLoss(220);
        $trade->setTakeProfit(150);
        $trade->setLots(1);
        $trade->setStatus(Trade::STATUS_CLOSE);
        $trade->setDescription('');

        return $trade;
    }

    public function load(ObjectManager $manager): void
    {
        $trade = self::getLongTrade();
        $trade->setStock($this->getReference(StockFixture::GAZP));
        $trade->setAccaunt($this->getReference(AccauntFixture::ACCAUNT_ONE));
        $trade->setStrategy($this->getReference(StrategyFixture::MY_STRATEGY));
        //        $trade->setType('long');
        //        $trade->setOpenDateTime(new \DateTime('2024-03-01 08:05:19'));
        //        $trade->setOpenPrice(150.00);
        //        $trade->setStopLoss(100);
        //        $trade->setTakeProfit(200);
        //        $trade->setLots(1);
        //        $trade->setStatus('open');
        //        $trade->setDescription('');
        $manager->persist($trade);

        $trade2 = self::getShortTrade();
        $trade2->setStock($this->getReference(StockFixture::SBER));
        $trade2->setAccaunt($this->getReference(AccauntFixture::ACCAUNT_ONE));
        $trade2->setStrategy($this->getReference(StrategyFixture::MY_STRATEGY));
        //        $trade2->setType('short');
        //        $trade2->setOpenDateTime(new \DateTime('2024-03-05 10:05:19'));
        //        $trade2->setOpenPrice(300.00);
        //        $trade2->setStopLoss(350);
        //        $trade2->setTakeProfit(250);
        //        $trade2->setLots(1);
        //        $trade2->setStatus('open');
        //        $trade2->setDescription('');
        $manager->persist($trade2);
        $manager->flush();
    }
}
