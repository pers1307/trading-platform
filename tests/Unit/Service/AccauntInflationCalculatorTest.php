<?php

namespace App\Tests\Unit\Service;

use App\Entity\Accaunt;
use App\Entity\AccauntHistory;
use App\Entity\AccauntInflation;
use App\Repository\AccauntInflationRepository;
use App\Repository\AccauntHistoryRepository;
use App\Service\AccauntInflation\AccauntInflationCalculator;
use App\Service\AccauntInflation\AccauntInflationRequest;
use App\Service\PercentCalculator\PercentCalculator;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class AccauntInflationCalculatorTest extends TestCase
{
    public function testCalculateUsesLatestHistoryAndCalculatesTermDaysFromPreviousSlice(): void
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

        $previousSlice = new AccauntInflation();
        $previousSlice->setAccaunt($accaunt);
        $previousSlice->setDate(new DateTimeImmutable('2025-12-01'));

        $inflationRepository = $this->createMock(AccauntInflationRepository::class);
        $inflationRepository
            ->method('findLatestBeforeDate')
            ->with(1, $this->isInstanceOf(\DateTimeInterface::class))
            ->willReturn($previousSlice);

        $percentCalculator = new PercentCalculator();

        $calculator = new AccauntInflationCalculator($historyRepository, $inflationRepository, $percentCalculator);

        $input = new AccauntInflationRequest(
            accaunt: $accaunt,
            movementAmount: 100.0,
            date: new DateTimeImmutable('2025-12-14'),
            depositRate: 16.5,
            centralBankKeyRate: 16.5,
        );

        $result = $calculator->calculate($input);

        $this->assertSame(1100.0, $result->getAccauntBalance());
        $this->assertSame(1105.0, $result->getAccauntInflationBalance());
        $this->assertSame(1105.0, $result->getAccauntDepositBalance());
        $this->assertSame(16.5, $result->getCentralBankKeyRate());
        $this->assertSame(16.5, $result->getDepositRate());
    }

    public function testCalculateUsesProvidedKeyRateWhenPresent(): void
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

        $inflationRepository = $this->createMock(AccauntInflationRepository::class);
        $inflationRepository
            ->method('findLatestBeforeDate')
            ->willReturn(null);

        $percentCalculator = new PercentCalculator();

        $calculator = new AccauntInflationCalculator($historyRepository, $inflationRepository, $percentCalculator);

        $input = new AccauntInflationRequest(
            accaunt: $accaunt,
            movementAmount: 100.0,
            date: new DateTimeImmutable('2025-12-14'),
            depositRate: 5.0,
            centralBankKeyRate: 1.23,
        );

        $result = $calculator->calculate($input);

        $this->assertSame(1.23, $result->getCentralBankKeyRate());
        $this->assertSame(5.0, $result->getDepositRate());
        $this->assertSame(1100.0, $result->getAccauntBalance());
        $this->assertSame(1100.0, $result->getAccauntDepositBalance());
        $this->assertSame(1100.0, $result->getAccauntInflationBalance());
    }

    /**
     * Боевой пример
     */
    public function testRealData(): void
    {
        $accaunt = new Accaunt();
        $this->setId($accaunt, 1);

        $latestHistory = new AccauntHistory();
        $latestHistory->setBalance(149574.0);
        $this->setId($latestHistory, 1);

        $historyRepository = $this->createMock(AccauntHistoryRepository::class);
        $historyRepository
            ->method('findLatestByAccauntId')
            ->with(1)
            ->willReturn($latestHistory);

        $previousSlice = new AccauntInflation();
        $previousSlice->setAccaunt($accaunt);
        $previousSlice->setDate(new DateTimeImmutable('2025-12-01'));

        $inflationRepository = $this->createMock(AccauntInflationRepository::class);
        $inflationRepository
            ->method('findLatestBeforeDate')
            ->with(1, $this->isInstanceOf(\DateTimeInterface::class))
            ->willReturn($previousSlice);

        $percentCalculator = new PercentCalculator();

        $calculator = new AccauntInflationCalculator($historyRepository, $inflationRepository, $percentCalculator);

        $input = new AccauntInflationRequest(
            accaunt: $accaunt,
            movementAmount: 0.0,
            date: new DateTimeImmutable('2026-01-01'),
            depositRate: 15.32,
            centralBankKeyRate: 17.0,
        );

        $result = $calculator->calculate($input);

        $this->assertSame(149574.0, $result->getAccauntBalance());
        $this->assertSame(151733.0, $result->getAccauntInflationBalance());
        $this->assertSame(151520.0, $result->getAccauntDepositBalance());
        $this->assertSame(17.0, $result->getCentralBankKeyRate());
        $this->assertSame(15.32, $result->getDepositRate());
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
