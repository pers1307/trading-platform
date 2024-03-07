<?php

namespace Integration;

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

        $this->assertCount(7, $result);
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
