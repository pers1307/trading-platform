<?php

namespace App\Dto;

class MoexStock
{
    public function __construct(
        private readonly string $title,
        private readonly string $secId,
        private readonly int $lotSize,
        private readonly float $minStep,
        private readonly float $price,
        private readonly float $open,
        private readonly float $high,
        private readonly float $low,
    ) {
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSecId(): string
    {
        return $this->secId;
    }

    public function getLotSize(): int
    {
        return $this->lotSize;
    }

    public function getMinStep(): float
    {
        return $this->minStep;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getOpen(): float
    {
        return $this->open;
    }

    public function getHigh(): float
    {
        return $this->high;
    }

    public function getLow(): float
    {
        return $this->low;
    }
}
