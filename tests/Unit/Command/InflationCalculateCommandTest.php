<?php

namespace App\Tests\Unit\Command;

use App\Command\InflationCalculateCommand;
use App\Entity\Accaunt;
use App\Entity\AccauntInflation;
use App\Repository\AccauntInflationRepository;
use App\Repository\AccauntRepository;
use App\Service\AccauntInflation\AccauntInflationCalculator;
use App\Service\AccauntInflation\AccauntInflationResponse;
use App\Service\CentralBankKeyRateService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class InflationCalculateCommandTest extends TestCase
{
    public function testCommandSavesResultWithProvidedDate(): void
    {
        $accaunt = new Accaunt();
        $this->setId($accaunt, 1);

        $accauntRepository = $this->createMock(AccauntRepository::class);
        $accauntRepository
            ->method('find')
            ->with(1)
            ->willReturn($accaunt);

        $accauntInflationRepository = $this->createMock(AccauntInflationRepository::class);
        $accauntInflationRepository
            ->method('findOneBy')
            ->willReturn(null);

        $resultDate = new DateTimeImmutable('2025-12-14');
        $calculatorResult = new AccauntInflationResponse(
            movementAmount: 100.0,
            centralBankKeyRate: 16.5,
            depositRate: 16.5,
            accauntBalance: 1100.0,
            accauntInflationBalance: 944.2,
            accauntDepositBalance: 1281.5,
            date: $resultDate,
        );

        $calculator = $this->createMock(AccauntInflationCalculator::class);
        $calculator
            ->method('calculate')
            ->willReturn($calculatorResult);

        $persisted = null;
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->callback(static function ($entity) use (&$persisted): bool {
                $persisted = $entity;

                return $entity instanceof AccauntInflation;
            }));
        $entityManager
            ->expects($this->once())
            ->method('flush');

        $centralBankKeyRateService = $this->createMock(CentralBankKeyRateService::class);
        $centralBankKeyRateService
            ->expects($this->once())
            ->method('getLatestKeyRate')
            ->willReturn(16.5);

        $command = new InflationCalculateCommand(
            $accauntRepository,
            $accauntInflationRepository,
            $calculator,
            $centralBankKeyRateService,
            $entityManager,
        );

        $tester = new CommandTester($command);
        $exitCode = $tester->execute([
            'movement_amount' => '100',
            'accaunt_id' => '1',
            'deposit_rate' => '16.5',
            'date' => '2025-12-14',
        ]);

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('Счет 1 (2025-12-14)', $tester->getDisplay());
        $this->assertInstanceOf(AccauntInflation::class, $persisted);
        $this->assertSame($accaunt, $persisted->getAccaunt());
        $this->assertSame($resultDate->format('Y-m-d'), $persisted->getDate()->format('Y-m-d'));
        $this->assertSame(100.0, $persisted->getMovementAmount());
        $this->assertSame(16.5, $persisted->getCentralBankKeyRate());
        $this->assertSame(16.5, $persisted->getDepositRate());
        $this->assertSame(1100.0, $persisted->getAccauntBalance());
        $this->assertSame(944.2, $persisted->getAccauntInflationBalance());
        $this->assertSame(1281.5, $persisted->getAccauntDepositBalance());
    }

    public function testCommandPassesProvidedKeyRateToCalculator(): void
    {
        $accaunt = new Accaunt();
        $this->setId($accaunt, 1);

        $accauntRepository = $this->createMock(AccauntRepository::class);
        $accauntRepository
            ->method('find')
            ->with(1)
            ->willReturn($accaunt);

        $accauntInflationRepository = $this->createMock(AccauntInflationRepository::class);
        $accauntInflationRepository
            ->method('findOneBy')
            ->willReturn(null);

        $resultDate = new DateTimeImmutable('2025-12-14');
        $calculatorResult = new AccauntInflationResponse(
            movementAmount: 100.0,
            centralBankKeyRate: 1.23,
            depositRate: 1.23,
            accauntBalance: 1100.0,
            accauntInflationBalance: 1086.63,
            accauntDepositBalance: 1113.53,
            date: $resultDate,
        );

        $calculator = $this->createMock(AccauntInflationCalculator::class);
        $calculator
            ->expects($this->once())
            ->method('calculate')
            ->with($this->callback(static function ($request): bool {
                return $request instanceof \App\Service\AccauntInflation\AccauntInflationRequest
                    && $request->centralBankKeyRate === 1.23;
            }))
            ->willReturn($calculatorResult);

        $persisted = null;
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->callback(static function ($entity) use (&$persisted): bool {
                $persisted = $entity;

                return $entity instanceof AccauntInflation;
            }));
        $entityManager
            ->expects($this->once())
            ->method('flush');

        $centralBankKeyRateService = $this->createMock(CentralBankKeyRateService::class);
        $centralBankKeyRateService
            ->expects($this->never())
            ->method('getLatestKeyRate');

        $command = new InflationCalculateCommand(
            $accauntRepository,
            $accauntInflationRepository,
            $calculator,
            $centralBankKeyRateService,
            $entityManager,
        );

        $tester = new CommandTester($command);
        $exitCode = $tester->execute([
            'movement_amount' => '100',
            'accaunt_id' => '1',
            'deposit_rate' => '10',
            'date' => '2025-12-14',
            'central_bank_key_rate' => '1.23',
        ]);

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('Счет 1 (2025-12-14)', $tester->getDisplay());
        $this->assertInstanceOf(AccauntInflation::class, $persisted);
        $this->assertSame($accaunt, $persisted->getAccaunt());
        $this->assertSame($resultDate->format('Y-m-d'), $persisted->getDate()->format('Y-m-d'));
        $this->assertSame(100.0, $persisted->getMovementAmount());
        $this->assertSame(1.23, $persisted->getCentralBankKeyRate());
        $this->assertSame(1.23, $persisted->getDepositRate());
        $this->assertSame(1100.0, $persisted->getAccauntBalance());
        $this->assertSame(1086.63, $persisted->getAccauntInflationBalance());
        $this->assertSame(1113.53, $persisted->getAccauntDepositBalance());
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
