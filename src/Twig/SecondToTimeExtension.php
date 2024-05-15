<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @todo написать тесты
 */
class SecondToTimeExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('toDays', [$this, 'convertSecondToDays']),
            new TwigFilter('toHours', [$this, 'convertSecondToHours']),
        ];
    }

    public function convertSecondToDays(int $second): string
    {
        return round($second / 24 / 3600);
    }

    public function convertSecondToHours(int $second): string
    {
        return round($second / 3600);
    }
}
