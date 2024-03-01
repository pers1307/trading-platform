<?php

namespace Integration;

use App\Repository\StockRepository;
use App\Service\DictionaryStockService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DictionaryStockServiceTest extends KernelTestCase
{
    public function testFindAll(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $stockRepositoryStub = $this->createMock(StockRepository::class);
        $stockRepositoryStub
            ->expects(self::once())
            ->method('findAll')
            ->willReturn([]);

        $container->set(StockRepository::class, $stockRepositoryStub);
        $dictionaryStockService = $container->get(DictionaryStockService::class);

        $result = $dictionaryStockService->findAll();

        $this->assertIsArray($result);
    }

    public function testFindAllDb(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $dictionaryStockService = $container->get(DictionaryStockService::class);
        $result = $dictionaryStockService->findAll();

        $this->assertCount(2, $result);
    }
}
