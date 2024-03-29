<?php

namespace App\Constant;

class FinancialType
{
    const TQBR = 'TQBR';
    const TQPI = 'TQPI';

    public static function getAll(): array
    {
        return [
            self::TQBR,
            self::TQPI,
        ];
    }
}
