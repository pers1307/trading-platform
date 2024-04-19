<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class CalculateStateFormService
{
    public function __construct(
        private readonly RequestStack $requestStack
    ) {
    }

    public function getAccauntId(): int
    {
        $session = $this->requestStack->getSession();
        return $session->get('accauntId');
    }

    public function setAccauntId(int $accauntId): static
    {
        $session = $this->requestStack->getSession();
        $session->set('accauntId', $accauntId);

        return $this;
    }

    public function getStockId(): int
    {
        $session = $this->requestStack->getSession();
        return $session->get('stockId');
    }

    public function setStockId(int $stockId): static
    {
        $session = $this->requestStack->getSession();
        $session->set('stockId', $stockId);

        return $this;
    }

    public function getStrategyId(): int
    {
        $session = $this->requestStack->getSession();
        return $session->get('strategyId');
    }

    public function setStrategyId(int $strategyId): static
    {
        $session = $this->requestStack->getSession();
        $session->set('strategyId', $strategyId);

        return $this;
    }

    public function getType(): string
    {
        $session = $this->requestStack->getSession();
        return $session->get('type');
    }

    public function setType(string $type): static
    {
        $session = $this->requestStack->getSession();
        $session->set('type', $type);
        return $this;
    }

    public function getStockPrice(): ?float
    {
        $session = $this->requestStack->getSession();
        return $session->get('stockPrice');
    }

    public function setStockPrice(?float $stockPrice): static
    {
        $session = $this->requestStack->getSession();
        $session->set('stockPrice', $stockPrice);
        return $this;
    }

    public function getStopLossPrice(): ?float
    {
        $session = $this->requestStack->getSession();
        return $session->get('stopLossPrice');
    }

    public function setStopLossPrice(?float $stopLossPrice): static
    {
        $session = $this->requestStack->getSession();
        $session->set('stopLossPrice', $stopLossPrice);
        return $this;
    }

    public function getTakeProfitPrice(): ?float
    {
        $session = $this->requestStack->getSession();
        return $session->get('takeProfitPrice');
    }

    public function setTakeProfitPrice(?float $takeProfitPrice): static
    {
        $session = $this->requestStack->getSession();
        $session->set('takeProfitPrice', $takeProfitPrice);
        return $this;
    }

    public function getLots(): int
    {
        $session = $this->requestStack->getSession();
        return $session->get('lots');
    }

    public function setLots(int $lots): static
    {
        $session = $this->requestStack->getSession();
        $session->set('lots', $lots);
        return $this;
    }

    public function clear(): void
    {
        $session = $this->requestStack->getSession();
        $session->clear();
    }
}
