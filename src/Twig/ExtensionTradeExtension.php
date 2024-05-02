<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ExtensionTradeExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('tradeResult', [$this, 'formatTradeResult']),
        ];
    }

    public function formatTradeResult(?float $number): string
    {
        if (is_null($number)) {
            return '';
        }

        return number_format($number, 2, '.', ' ');
    }
}
