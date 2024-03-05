<?php

namespace App\Exception;

use Exception;

class TradeHasNotClosePriceException extends \Exception
{
    public function __construct($message = null, $code = 0, Exception $previous = null)
    {
        if (is_null($message)) {
            $message = 'Трейд не имеет цены закрытия';
        }

        parent::__construct($message, $code, $previous);
    }
}
