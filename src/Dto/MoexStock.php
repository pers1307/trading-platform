<?php

namespace App\Dto;

readonly class MoexStock
{
    public function __construct(
        private string $title,
        private string $boardId,
        private string $secId,
        private int $lotSize,
        private float $minStep,
        private float $price,
        private float $open,
        private float $high,
        private float $low,
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

    public function getBoardId(): string
    {
        return $this->boardId;
    }
}
