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

    /**
     * @todo эту функцию точно нужно будет покрыть тестами
     * @todo вторым аргументов закидывать минимальный шаг цены для правильного форматирования!
     */
    public function formatStockPrice(?float $number): string
    {
        if (is_null($number)) {
            return '';
        }

        if ($number === 0.000005) {
            return number_format($number, 6, '.', ' ');
        } elseif ($number <= 0.00001) {
            return number_format($number, 6, '.', ' ');
        }

        return number_format($number, 2, '.', ' ');
    }
}
