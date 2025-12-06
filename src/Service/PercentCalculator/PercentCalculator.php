<?php

namespace App\Service\PercentCalculator;

use InvalidArgumentException;

class PercentCalculator
{
    public function calculate(PercentCalculatorRequest $request): PercentCalculatorResponse
    {
        $this->assertValid($request);

        $interest = round(
            $request->principal
            * $request->annualRatePercent
            * $request->termDays
            / $request->daysInYear
            / 100,
            2
        );

        $total = round($request->principal + $interest, 2);

        return new PercentCalculatorResponse($interest, $total);
    }

    private function assertValid(PercentCalculatorRequest $request): void
    {
        if ($request->principal < 0) {
            throw new InvalidArgumentException('Сумма вклада должна быть неотрицательной.');
        }

        if ($request->annualRatePercent < 0) {
            throw new InvalidArgumentException('Годовая ставка должна быть неотрицательной.');
        }

        if ($request->termDays < 0) {
            throw new InvalidArgumentException('Срок в днях должен быть неотрицательным.');
        }

        if ($request->daysInYear <= 0) {
            throw new InvalidArgumentException('Количество дней в году должно быть больше нуля.');
        }
    }
}
