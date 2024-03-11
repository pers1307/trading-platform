<?php

namespace Integration;

use App\DataFixture\TradeFixture;
use App\Repository\TradeRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TradeRepositoryTest extends KernelTestCase
{
    public function testFindAll(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $tradeRepository = $container->get(TradeRepository::class);
        $result = $tradeRepository->findAll();

        $countTradres = count(TradeFixture::getTrades()) + 2;

        $this->assertCount($countTradres, $result);
    }

    public function testGetStrategiesByAccaunts(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $tradeRepository = $container->get(TradeRepository::class);
        $result = $tradeRepository->getStrategiesByAccaunts();

        $this->assertCount(2, $result);
    }
}
