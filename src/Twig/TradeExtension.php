<?php

namespace App\Twig;

use App\Entity\Trade;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TradeExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('tradeStatus', [$this, 'formatTradeStatus'], ['is_safe' => ['html']]),
            new TwigFilter('tradeType', [$this, 'formatTradeType'], ['is_safe' => ['html']]),
        ];
    }

    public function formatTradeStatus(string $status): string
    {
        if ($status === Trade::STATUS_OPEN) {
            return '<span class="badge bg-info text-dark">Open</span>';
        } elseif ($status === Trade::STATUS_CLOSE) {
            return '<span class="badge bg-secondary">Close</span>';
        }

        return '<span class="badge bg-warning text-dark">Unknown</span>';
    }

    public function formatTradeType(string $type): string
    {
        if ($type === Trade::TYPE_LONG) {
            return '<span class="badge bg-success">Long</span>';
        } elseif ($type === Trade::TYPE_SHORT) {
            return '<span class="badge bg-danger">Short</span>';
        }

        return '<span class="badge bg-warning text-dark">Unknown</span>';
    }
}
