<?php

namespace App\Service\AccauntInflation;

use DateTimeInterface;

final readonly class AccauntInflationResponse
{
    public function __construct(
        private float $movementAmount,
        private float $centralBankKeyRate,
        private float $depositRate,
        private float $accauntBalance,
        private float $accauntInflationBalance,
        private float $accauntDepositBalance,
        private DateTimeInterface $date,
    ) {
    }

    public function getMovementAmount(): float
    {
        return $this->movementAmount;
    }

    public function getCentralBankKeyRate(): float
    {
        return $this->centralBankKeyRate;
    }

    public function getDepositRate(): float
    {
        return $this->depositRate;
    }

    public function getAccauntBalance(): float
    {
        return $this->accauntBalance;
    }

    public function getAccauntInflationBalance(): float
    {
        return $this->accauntInflationBalance;
    }

    public function getAccauntDepositBalance(): float
    {
        return $this->accauntDepositBalance;
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }
}
