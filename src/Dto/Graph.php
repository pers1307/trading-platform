<?php

namespace App\Dto;

class Graph
{
    public function __construct(
        private readonly string $formatData
    ) {
    }

    public function getFormatData(): string
    {
        return $this->formatData;
    }
}
