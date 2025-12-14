<?php

namespace App\Service\AccauntInflation;

use App\Repository\AccauntHistoryRepository;
use App\Service\CentralBankKeyRateService;

class AccauntInflationCalculator
{
    public function __construct(
        private readonly CentralBankKeyRateService $centralBankKeyRateService,
        private readonly AccauntHistoryRepository $accauntHistoryRepository,
    ) {
    }

    public function calculate(AccauntInflationRequest $request): AccauntInflationResponse
    {
        $accaunt = $request->accaunt;
        $movementAmount = $request->movementAmount;
        $date = $request->date;

        $latestHistory = $this->accauntHistoryRepository->findLatestByAccauntId($accaunt->getId());
        $baseBalance = $latestHistory?->getBalance() ?? 0.0;

        $accauntBalance = $baseBalance + $movementAmount;

        $centralBankKeyRate = $this->centralBankKeyRateService->getLatestKeyRate();
        $depositRate = $centralBankKeyRate;

        $inflationFactor = 1 + ($centralBankKeyRate / 100);
        $accauntInflationBalance = $inflationFactor > 0 ? $accauntBalance / $inflationFactor : $accauntBalance;

        $accauntDepositBalance = $accauntBalance * (1 + ($depositRate / 100));

        return new AccauntInflationResponse(
            movementAmount: $movementAmount,
            centralBankKeyRate: $centralBankKeyRate,
            depositRate: $depositRate,
            accauntBalance: $accauntBalance,
            accauntInflationBalance: $accauntInflationBalance,
            accauntDepositBalance: $accauntDepositBalance,
            date: $date,
        );
    }
}
