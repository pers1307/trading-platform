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
                $securityItem[9],
                $securityItem[1],
                $securityItem[0],
                intval($securityItem[4]),
                floatval($securityItem[14]),
                floatval($marketItem[12]),
                floatval($marketItem[9]),
                floatval($marketItem[11]),
                floatval($marketItem[10]),
            );
        }, $data['securities']['data'], $data['marketdata']['data']);
    }
}
