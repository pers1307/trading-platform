<?php

namespace Integration;

use App\Repository\TradeRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TradeServiceTest extends KernelTestCase
{
    public function testFindAll(): void
    {
        $this->markTestIncomplete();
//        self::bootKernel();
//        $container = static::getContainer();
//        $dictionaryStockService = $container->get(TradeRepository::class);
//        $result = $dictionaryStockService->findAll();
//
//        $this->assertCount(2, $result);
    }
//
//    public function testGetStrategiesByAccaunts(): void
//    {
//        self::bootKernel();
//        $container = static::getContainer();
//        $dictionaryStockService = $container->get(TradeRepository::class);
//        $result = $dictionaryStockService->getStrategiesByAccaunts();
//
//        $this->assertCount(1, $result);
//    }
}
