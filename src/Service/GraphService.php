<?php

namespace App\Service;

use App\Dto\Graph;
use Closure;

class GraphService
{
    public function format(array $items, Closure $labelFunction, Closure $valueFunction): Graph
    {
        return new Graph(
            json_encode(
                [
                    'labels' => array_map($labelFunction, array_keys($items), $items),
                    'values' => array_map($valueFunction, array_keys($items), $items),
                ]
            )
        );
    }
}
