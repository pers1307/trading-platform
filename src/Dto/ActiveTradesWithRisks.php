<?php

namespace App\Dto;

class ActiveTradesWithRisks
{
    public function __construct(
        private readonly array $trades

    ) {
    }

//    public function getFormatData(): string
//    {
//        return $this->formatData;
//    }
}
