<?php

namespace App\DataFixture;

class MoexApiFixture
{
    public static function getSBERData(): array
    {
        return [
            'securities' => [
                'data' => [
                    [
                        'SBER',
                        1,
                        2,
                        3,
                        10,
                        5,
                        6,
                        7,
                        8,
                        'Сбербанк России ПАО ао',
                        10,
                        11,
                        12,
                        13,
                        0.01,
                    ],
                ],
            ],
            'marketdata' => [
                'data' => [
                    [
                        0,
                        1,
                        2,
                        3,
                        4,
                        5,
                        6,
                        7,
                        8,
                        100,
                        90,
                        120,
                        100,
                        13,
                        14,
                    ],
                ],
            ],
        ];
    }

    public static function getGAZPData(): array
    {
        return [
            'securities' => [
                'data' => [
                    [
                        'GAZP',
                        1,
                        2,
                        3,
                        10,
                        5,
                        6,
                        7,
                        8,
                        '"Газпром" (ПАО) ао',
                        10,
                        11,
                        12,
                        13,
                        0.01,
                    ],
                ],
            ],
            'marketdata' => [
                'data' => [
                    [
                        0,
                        1,
                        2,
                        3,
                        4,
                        5,
                        6,
                        7,
                        8,
                        200,
                        190,
                        220,
                        200,
                        13,
                        14,
                    ],
                ],
            ],
        ];
    }

    public static function getOZONData(): array
    {
        return [
            'securities' => [
                'data' => [
                    [
                        'OZON',
                        1,
                        2,
                        3,
                        1,
                        5,
                        6,
                        7,
                        8,
                        'АДР Ozon Holdings PLC ORD SHS',
                        10,
                        11,
                        12,
                        13,
                        0.05,
                    ],
                ],
            ],
            'marketdata' => [
                'data' => [
                    [
                        0,
                        1,
                        2,
                        3,
                        4,
                        5,
                        6,
                        7,
                        8,
                        200,
                        190,
                        220,
                        200,
                        13,
                        14,
                    ],
                ],
            ],
        ];
    }

    public static function getAll(): array
    {
        return [
            'securities' => [
                'data' => [
                    self::getSBERData()['securities']['data'][0],
                    self::getGAZPData()['securities']['data'][0],
                    self::getOZONData()['securities']['data'][0],
                ],
            ],
            'marketdata' => [
                'data' => [
                    self::getSBERData()['marketdata']['data'][0],
                    self::getGAZPData()['marketdata']['data'][0],
                    self::getOZONData()['marketdata']['data'][0],
                ],
            ],
        ];
    }
}
