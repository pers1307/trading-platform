<?php

namespace Integration;

use App\Repository\AccauntRepository;
use App\Service\AccauntService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AccauntServiceTest extends KernelTestCase
{
    /**
     * Это больше юнит тест нежели интеграционный
     * Интеграционный тест, вытаскивает данные из БД и проверяет её взаимодействие
     * Это бесполезный тест! Тест нуже уже проверяет, что это работает!
     * Моки нужны для изоляции и используются в Юнитах
     * Либо здесь, если нужно заменить зависимость на тестовую, при взаимодействии с БД
     * @return void
     */
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
