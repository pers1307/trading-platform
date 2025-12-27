<?php

namespace App\Service\AccauntInflation;

use App\Entity\AccauntInflation;

class AccauntInflationGraphDataFormatter
{
    /**
     * @param AccauntInflation[] $items
     */
    public function format(array $items): string
    {
        return json_encode([
            'labels' => array_map(
                static fn(AccauntInflation $item) => $item->getDate()->format('Y-m-d'),
                $items
            ),
            'depositValues' => array_map(
                static fn(AccauntInflation $item) => $item->getAccauntDepositBalance(),
                $items
            ),
            'balanceValues' => array_map(
                static fn(AccauntInflation $item) => $item->getAccauntBalance(),
                $items
            ),
            'inflationValues' => array_map(
                static fn(AccauntInflation $item) => $item->getAccauntInflationBalance(),
                $items
            ),
        ]);
    }
}
