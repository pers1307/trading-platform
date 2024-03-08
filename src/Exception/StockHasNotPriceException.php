<?php

namespace App\Exception;

use Exception;

class StockHasNotPriceException extends \Exception
{
    public function __construct($message = null, $code = 0, Exception $previous = null)
    {
        if (is_null($message)) {
            $message = 'Акция не имеет значение текущей цены';
        }

        parent::__construct($message, $code, $previous);
    }
}
