<?php

namespace App\Tests\Unit\Service;

use App\Service\PercentCalculator\PercentCalculator;
use App\Service\PercentCalculator\PercentCalculatorRequest;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PercentCalculatorTest extends TestCase
{
    private PercentCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new PercentCalculator();
    }

    /**
     * @dataProvider validCases
     */
    public function testCalculateReturnsExpectedAmounts(
        float $principal,
        float $rate,
        int $termDays,
        int $daysInYear,
        float $expectedInterest,
        float $expectedTotal
    ): void {
        $request = new PercentCalculatorRequest($principal, $rate, $termDays, $daysInYear);

        $response = $this->calculator->calculate($request);

        self::assertSame($expectedInterest, $response->interestAmount);
        self::assertSame($expectedTotal, $response->totalAmount);
    }

    public function validCases(): array
    {
        return [
            'пример 92 дня, 17.5%, 366 дней' => [200000.0, 17.5, 92, 366, 8797.81, 208797.81],
            'пример год, 17.5%' => [200000.0, 17.5, 365, 365, 35000.0, 235000.0],
            'нулевая ставка' => [150000.0, 0.0, 180, 365, 0.0, 150000.0],
            'ноль дней' => [100000.0, 12.5, 0, 365, 0.0, 100000.0],
        ];
    }

    /**
     * @dataProvider invalidCases
     */
    public function testCalculateValidatesInput(
        float $principal,
        float $rate,
        int $termDays,
        int $daysInYear,
        string $expectedMessage
    ): void {
        $request = new PercentCalculatorRequest($principal, $rate, $termDays, $daysInYear);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);

        $this->calculator->calculate($request);
    }

    public function invalidCases(): array
    {
        return [
            'отрицательный вклад' => [-1.0, 10.0, 30, 365, 'Сумма вклада должна быть неотрицательной.'],
            'отрицательная ставка' => [100000.0, -5.0, 30, 365, 'Годовая ставка должна быть неотрицательной.'],
            'отрицательный срок' => [100000.0, 10.0, -1, 365, 'Срок в днях должен быть неотрицательным.'],
            'некорректный календарь' => [100000.0, 10.0, 30, 0, 'Количество дней в году должно быть больше нуля.'],
        ];
    }
}
