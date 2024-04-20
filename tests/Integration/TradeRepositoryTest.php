<?php

namespace App\Tests\Integration;

use App\DataFixture\TradeFixture;
use App\Entity\Trade;
use App\Exception\UnknownStatusException;
use App\Repository\TradeRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * todo: заменить на другой более поведенческий тест
 */
class TradeRepositoryTest extends KernelTestCase
{
    public function testFindAll(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $tradeRepository = $container->get(TradeRepository::class);
        $result = $tradeRepository->findAll();

        $countTradres = count(TradeFixture::getTrades()) + 3;

        $this->assertCount($countTradres, $result);
    }

    public function testGetCloseTradeStrategiesByAccaunts(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $tradeRepository = $container->get(TradeRepository::class);
        $result = $tradeRepository->getStrategiesByAccaunts(Trade::STATUS_CLOSE);

        $this->assertCount(2, $result);
    }

    public function testGetOpenTradeStrategiesByAccaunts(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $tradeRepository = $container->get(TradeRepository::class);
        $result = $tradeRepository->getStrategiesByAccaunts(Trade::STATUS_OPEN);

        $this->assertCount(2, $result);
    }

    public function testGetUnknownTradeStatusStrategiesByAccaunts(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $tradeRepository = $container->get(TradeRepository::class);

        $this->expectException(UnknownStatusException::class);

        $tradeRepository->getStrategiesByAccaunts('Hello');
    }
}
