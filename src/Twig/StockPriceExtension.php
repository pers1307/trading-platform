<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class StockPriceExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('stockPrice', [$this, 'formatStockPrice']),
        ];
    }

    public function formatStockPrice(?float $number, float $minStep): string
    {
        if (is_null($number)) {
            return '';
        }

        $decimals = $this->getDecimals($minStep);
        if ($minStep < 1) {
            if ($this->normalize($number, $decimals) % $this->normalize($minStep, $decimals) !== 0) {
                return 'Цена не соответствует шагу цены';
            }
        } else {
            if (fmod($number, $minStep) !== 0.0) {
                return 'Цена не соответствует шагу цены';
            }
        }

        return number_format($number, $decimals, '.', '');
    }

    private function normalize(float $number, int $decimals): float
    {
        return $number * (10 ** $decimals);
    }

    private function getDecimals(float $valueAsString): int
    {
        $valueAsString = (string)$valueAsString;
        if (preg_match('/(\d).0E-(\d)/', $valueAsString, $matches)) {
            $decimals = $matches[2];
        } else {
            $explodeDigits = explode('.', $valueAsString);
            $decimals = isset($explodeDigits[1]) ? strlen($explodeDigits[1]) : 0;
        }

        return $decimals;
    }
}
