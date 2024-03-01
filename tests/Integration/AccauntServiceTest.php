<?php

namespace Integration;

use App\Repository\AccauntRepository;
use App\Service\AccauntService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AccauntServiceTest extends KernelTestCase
{
    public function testFindAll(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $accauntRepositoryStub = $this->createMock(AccauntRepository::class);
        $accauntRepositoryStub
            ->expects(self::once())
            ->method('findAll')
            ->willReturn([]);

        $container->set(AccauntRepository::class, $accauntRepositoryStub);
        $dictionaryStockService = $container->get(AccauntService::class);

        $result = $dictionaryStockService->findAll();

        $this->assertIsArray($result);
    }

    public function testFindAllDb(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $accauntService = $container->get(AccauntService::class);
        $result = $accauntService->findAll();

        $this->assertCount(2, $result);
    }
}
