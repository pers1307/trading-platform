<?php

namespace App\Service\AccauntInflation;

use App\Entity\AccauntInflation;
use App\Repository\AccauntInflationRepository;
use App\Repository\AccauntHistoryRepository;
use App\Service\PercentCalculator\PercentCalculator;
use App\Service\PercentCalculator\PercentCalculatorRequest;

class AccauntInflationCalculator
{
    public function __construct(
        private readonly AccauntHistoryRepository $accauntHistoryRepository,
        private readonly AccauntInflationRepository $accauntInflationRepository,
        private readonly PercentCalculator $percentCalculator,
    ) {
    }

    public function calculate(AccauntInflationRequest $request): AccauntInflationResponse
    {
        $latestHistory = $this->accauntHistoryRepository->findLatestByAccauntId($request->accaunt->getId());
        $historyBalance = $latestHistory?->getBalance() ?? 0.0;
        $accauntBalance = $historyBalance + $request->movementAmount;

        /** @var AccauntInflation $previousSlice */
        $previousSlice = $this->accauntInflationRepository->findLatestBeforeDate($request->accaunt->getId(), $request->date);
        $termDays = 0;
        $accauntInflationHistoryBalance = 0;
        $accauntDepositHistoryBalance = 0;
        if ($previousSlice !== null) {
            $days = (int) $previousSlice->getDate()->diff($request->date)->format('%r%a');
            $termDays = max(0, $days);
            $accauntInflationHistoryBalance = $previousSlice->getAccauntInflationBalance();
            $accauntDepositHistoryBalance = $previousSlice->getAccauntDepositBalance();
        }

        $centralBankKeyRate = $request->centralBankKeyRate;
        $depositRate = $request->depositRate;

        $depositResponse = $this->percentCalculator->calculate(new PercentCalculatorRequest(
            principal: $accauntDepositHistoryBalance,
            annualRatePercent: $depositRate,
            termDays: $termDays,
            daysInYear: 365,
        ));

        $inflationResponse = $this->percentCalculator->calculate(new PercentCalculatorRequest(
            principal: $accauntInflationHistoryBalance,
            annualRatePercent: $centralBankKeyRate,
            termDays: $termDays,
            daysInYear: 365,
        ));

        return new AccauntInflationResponse(
            movementAmount: $request->movementAmount,
            centralBankKeyRate: $centralBankKeyRate,
            depositRate: $depositRate,
            accauntBalance: $accauntBalance,
            accauntInflationBalance: intval($inflationResponse->totalAmount + $request->movementAmount),
            accauntDepositBalance: intval($depositResponse->totalAmount + $request->movementAmount),
            date: $request->date,
        );
    }
}
