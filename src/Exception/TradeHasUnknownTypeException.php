<?php

namespace App\Exception;

use Exception;

class TradeHasUnknownTypeException extends \Exception
{
    public function __construct($message = null, $code = 0, Exception $previous = null)
    {
        if (is_null($message)) {
            $message = 'Трейд имеет неизвестный тип';
        }

        parent::__construct($message, $code, $previous);
    }
}
