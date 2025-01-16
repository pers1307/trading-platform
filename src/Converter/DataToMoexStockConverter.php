<?php

namespace App\Converter;

use App\Dto\MoexStock;

class DataToMoexStockConverter
{
    /**
     * @return MoexStock[]
     */
    public function convert(array $data): array
    {
        return array_map(static function (array $securityItem, array $marketItem) {
            return new MoexStock(
                title: $securityItem[9],
                boardId: $securityItem[1],
                secId: $securityItem[0],
                lotSize: intval($securityItem[4]),
                minStep: floatval($securityItem[14]),
                price: floatval($marketItem[12]),
                open: floatval($marketItem[9]),
                high: floatval($marketItem[11]),
                low: floatval($marketItem[10]),
            );
        }, $data['securities']['data'], $data['marketdata']['data']);
    }
}
