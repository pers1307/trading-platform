<?php

namespace App\Tests\Unit\Service;

use App\Entity\Accaunt;
use App\Entity\AccauntHistory;
use App\Repository\AccauntHistoryRepository;
use App\Service\AccauntInflation\AccauntInflationCalculator;
use App\Service\AccauntInflation\AccauntInflationRequest;
use App\Service\CentralBankKeyRateService;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class AccauntInflationCalculatorTest extends TestCase
{
    public function testCalculateUsesLatestHistoryAndKeyRate(): void
    {
        $accaunt = new Accaunt();
        $this->setId($accaunt, 1);

        $latestHistory = new AccauntHistory();
        $latestHistory->setBalance(1000.0);
        $this->setId($latestHistory, 10);

        $historyRepository = $this->createMock(AccauntHistoryRepository::class);
        $historyRepository
            ->method('findLatestByAccauntId')
            ->with(1)
            ->willReturn($latestHistory);

        $centralBankKeyRateService = $this->createMock(CentralBankKeyRateService::class);
        $centralBankKeyRateService
            ->method('getLatestKeyRate')
            ->willReturn(16.5);

        $calculator = new AccauntInflationCalculator($centralBankKeyRateService, $historyRepository);

        $input = new AccauntInflationRequest(
            accaunt: $accaunt,
            movementAmount: 100.0,
            date: new DateTimeImmutable('2025-12-14'),
        );

        $result = $calculator->calculate($input);

        $this->assertSame(1100.0, $result->getAccauntBalance());
        $this->assertEqualsWithDelta(944.2, $result->getAccauntInflationBalance(), 0.01);
        $this->assertEqualsWithDelta(1281.5, $result->getAccauntDepositBalance(), 0.01);
        $this->assertSame(16.5, $result->getCentralBankKeyRate());
        $this->assertSame(16.5, $result->getDepositRate());
    }

    private function setId(object $entity, int $id): void
    {
        $reflection = new \ReflectionClass($entity);
        if (!$reflection->hasProperty('id')) {
            return;
        }

        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($entity, $id);
    }
}
