<?php

namespace Integration;

use App\Repository\TradeRepository;
use App\Service\TradeService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TradeServiceTest extends KernelTestCase
{
    public function testFindAll(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $stockRepositoryStub = $this->createMock(TradeRepository::class);
        $stockRepositoryStub
            ->expects(self::once())
            ->method('findAll')
            ->willReturn([]);

        $container->set(TradeRepository::class, $stockRepositoryStub);
        $dictionaryStockService = $container->get(TradeService::class);

        $result = $dictionaryStockService->findAll();

        $this->assertIsArray($result);
    }

    public function testFindAllDb(): void
    {
        /**
         * @todo последовательное добавление фикстур!
         */
        $this->markTestIncomplete('Мок данные');

        //        self::bootKernel();
        //        $container = static::getContainer();
        //        $dictionaryStockService = $container->get(DictionaryStockService::class);
        //        $result = $dictionaryStockService->findAll();
        //
        //        $this->assertCount(2, $result);
    }
}
