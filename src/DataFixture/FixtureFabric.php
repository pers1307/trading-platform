<?php

namespace App\DataFixture;

use App\Entity\Accaunt;
use App\Entity\RiskProfile;
use App\Entity\Strategy;
use App\Entity\Trade;

class FixtureFabric
{
    const SBER = 'sber';
    const GAZP = 'gazp';

    /**
     * @throws \Exception
     */
    public static function getLongTrade(string $status, string $stock): Trade
    {
        $trade = TradeFixture::getLongTrade($status);
        $trade->setAccaunt(self::getOneAccaunt());
        $trade->setStrategy(self::getMyStrategy());

        if (self::GAZP === $stock) {
            $trade->setStock(StockFixture::getGazp());
        } elseif (self::SBER === $stock) {
            $trade->setStock(StockFixture::getSber());
        }

        return $trade;
    }

    /**
     * @throws \Exception
     */
    public static function getShortTrade(string $status, string $stock): Trade
    {
        $trade = TradeFixture::getShortTrade($status);
        $trade->setAccaunt(self::getOneAccaunt());
        $trade->setStrategy(self::getMyStrategy());

        if (self::GAZP === $stock) {
            $trade->setStock(StockFixture::getGazp());
        } elseif (self::SBER === $stock) {
            $trade->setStock(StockFixture::getSber());
        }

        return $trade;
    }

    /**
     * @throws \Exception
     */
    public static function getOneAccaunt(): Accaunt
    {
        $accaunt = AccauntFixture::getOneAccaunt();

        $reflection = new \ReflectionClass($accaunt);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($accaunt, 1);

        return $accaunt;
    }

    public static function getMyStrategy(): Strategy
    {
        $strategy = StrategyFixture::getMyStrategy();

        $reflection = new \ReflectionClass($strategy);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($strategy, 1);

        return $strategy;
    }

    /**
     * @throws \Exception
     */
    public static function getRiskProfile(string $type): RiskProfile
    {
        $riskProfile = RiskProfileFixture::getRiskProfile($type);
        $riskProfile->setAccaunt(self::getOneAccaunt());
        $riskProfile->setStrategy(self::getMyStrategy());

        return $riskProfile;
    }
}
