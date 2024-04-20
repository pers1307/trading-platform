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

    /**
     * @throws \Exception
     * @todo: перевести на фабрики
     * через фабричные методы собирать объекты,
     * чтобы сделать тесты изолированными
     */
    public static function getLongTrade(string $status): Trade
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
        $trade->setStatus($status);
        $trade->setDescription('');

        return $trade;
    }

    /**
     * @throws \Exception
     */
    public static function getShortTrade(string $status): Trade
    {
        $trade = new Trade();
        $trade->setType(Trade::TYPE_SHORT);
        $trade->setOpenDateTime(new \DateTime('2024-03-01 10:05:19'));
        $trade->setOpenPrice(200.00);
        $trade->setCloseDateTime(new \DateTime('2024-03-05 10:05:19'));
        $trade->setClosePrice(220.00);
        $trade->setStopLoss(220);
        $trade->setTakeProfit(150);
        $trade->setLots(1);
        $trade->setStatus($status);
        $trade->setDescription('');

        return $trade;
    }

    /**
     * 5 сделок
     * long +
     * long -
     * short +
     * short -
     * short +
     * short + open
     * @throws \Exception
     */
    public static function getTrades(): array
    {
        return [
            (new Trade())
                ->setType(Trade::TYPE_LONG)
                ->setOpenDateTime(new \DateTime('2024-03-01 10:00:00'))
                ->setOpenPrice(200.00)
                ->setCloseDateTime(new \DateTime('2024-03-01 19:00:00'))
                ->setClosePrice(250.00)
                ->setStopLoss(150)
                ->setTakeProfit(250)
                ->setLots(1)
                ->setStatus(Trade::STATUS_CLOSE)
                ->setDescription(''),
            (new Trade())
                ->setType(Trade::TYPE_LONG)
                ->setOpenDateTime(new \DateTime('2024-03-02 10:00:00'))
                ->setOpenPrice(200.00)
                ->setCloseDateTime(new \DateTime('2024-03-02 19:00:00'))
                ->setClosePrice(190.00)
                ->setStopLoss(190)
                ->setTakeProfit(250)
                ->setLots(1)
                ->setStatus(Trade::STATUS_CLOSE)
                ->setDescription(''),
            (new Trade())
                ->setType(Trade::TYPE_SHORT)
                ->setOpenDateTime(new \DateTime('2024-03-03 10:00:00'))
                ->setOpenPrice(300.00)
                ->setCloseDateTime(new \DateTime('2024-03-03 19:00:00'))
                ->setClosePrice(250.00)
                ->setStopLoss(320)
                ->setTakeProfit(250)
                ->setLots(1)
                ->setStatus(Trade::STATUS_CLOSE)
                ->setDescription(''),
            (new Trade())
                ->setType(Trade::TYPE_SHORT)
                ->setOpenDateTime(new \DateTime('2024-03-04 10:00:00'))
                ->setOpenPrice(250.00)
                ->setCloseDateTime(new \DateTime('2024-03-04 19:00:00'))
                ->setClosePrice(280.00)
                ->setStopLoss(280)
                ->setTakeProfit(220)
                ->setLots(1)
                ->setStatus(Trade::STATUS_CLOSE)
                ->setDescription(''),
            (new Trade())
                ->setType(Trade::TYPE_SHORT)
                ->setOpenDateTime(new \DateTime('2024-03-05 10:00:00'))
                ->setOpenPrice(200.00)
                ->setCloseDateTime(new \DateTime('2024-03-05 19:00:00'))
                ->setClosePrice(150.00)
                ->setStopLoss(220)
                ->setTakeProfit(150)
                ->setLots(1)
                ->setStatus(Trade::STATUS_CLOSE)
                ->setDescription(''),
            (new Trade())
                ->setType(Trade::TYPE_SHORT)
                ->setOpenDateTime(new \DateTime('2024-03-06 10:00:00'))
                ->setOpenPrice(250.00)
                ->setStopLoss(300)
                ->setTakeProfit(200)
                ->setLots(1)
                ->setStatus(Trade::STATUS_OPEN)
                ->setDescription(''),
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $trade = self::getLongTrade(Trade::STATUS_CLOSE);
        $trade->setStock($this->getReference(StockFixture::GAZP));
        $trade->setAccaunt($this->getReference(AccauntFixture::ACCAUNT_ONE));
        $trade->setStrategy($this->getReference(StrategyFixture::MY_STRATEGY));
        $manager->persist($trade);

        $trade2 = self::getShortTrade(Trade::STATUS_CLOSE);
        $trade2->setStock($this->getReference(StockFixture::SBER));
        $trade2->setAccaunt($this->getReference(AccauntFixture::ACCAUNT_ONE));
        $trade2->setStrategy($this->getReference(StrategyFixture::MY_STRATEGY));
        $manager->persist($trade2);

        foreach (self::getTrades() as $trade) {
            $trade->setStock($this->getReference(StockFixture::GAZP));
            $trade->setAccaunt($this->getReference(AccauntFixture::ACCAUNT_TWO));
            $trade->setStrategy($this->getReference(StrategyFixture::MY_STRATEGY));

            $manager->persist($trade);
        }

        $trade = self::getLongTrade(Trade::STATUS_OPEN);
        $trade->setStock($this->getReference(StockFixture::GAZP));
        $trade->setAccaunt($this->getReference(AccauntFixture::ACCAUNT_ONE));
        $trade->setStrategy($this->getReference(StrategyFixture::EMPTY_STRATEGY));
        $manager->persist($trade);

        $manager->flush();
    }
}
