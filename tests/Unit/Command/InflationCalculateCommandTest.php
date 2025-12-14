<?php

namespace App\Tests\Unit\Command;

use App\Command\InflationCalculateCommand;
use App\Entity\Accaunt;
use App\Entity\AccauntInflation;
use App\Repository\AccauntInflationRepository;
use App\Repository\AccauntRepository;
use App\Service\AccauntInflation\AccauntInflationCalculator;
use App\Service\AccauntInflation\AccauntInflationResponse;
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

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(AccauntInflation::class));
        $entityManager
            ->expects($this->once())
            ->method('flush');

        $command = new InflationCalculateCommand(
            $accauntRepository,
            $accauntInflationRepository,
            $calculator,
            $entityManager,
        );

        $tester = new CommandTester($command);
        $exitCode = $tester->execute([
            'movement_amount' => '100',
            'accaunt_id' => '1',
            'date' => '2025-12-14',
        ]);

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('Счет 1 (2025-12-14)', $tester->getDisplay());
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
