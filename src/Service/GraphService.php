<?php

namespace App\Service;

use Closure;

class GraphService
{
    /**
     * @todo возможно когда то сделать это хелпером
     */
    public function format(array $items, Closure $labelFunction, Closure $valueFunction): string
    {
        return json_encode(
            [
                'labels' => array_map($labelFunction, array_keys($items), $items),
                'values' => array_map($valueFunction, array_keys($items), $items),
            ]
        );
    }
}
