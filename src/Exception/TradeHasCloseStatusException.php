<?php

namespace App\Exception;

use Exception;

class TradeHasCloseStatusException extends \Exception
{
    public function __construct($message = null, $code = 0, Exception $previous = null)
    {
        if (is_null($message)) {
            $message = 'Трейд имеет статус закрыто';
        }

        parent::__construct($message, $code, $previous);
    }
}
