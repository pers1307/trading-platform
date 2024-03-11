<?php

namespace App\Service;

use App\Dto\ExtensionTrade;

class CumulativeTotalService
{
    /**
     * @param ExtensionTrade[] $extensionTrades
     */
    public function calculate(array $extensionTrades): array
    {
        $result = [];
        $cumulativeTotal = 0.0;
        foreach ($extensionTrades as $extensionTrade) {
            $cumulativeTotal = round($cumulativeTotal + $extensionTrade->getTradeResult(), 2);
            $result[] = $cumulativeTotal;
        }

        return $result;
    }
}
