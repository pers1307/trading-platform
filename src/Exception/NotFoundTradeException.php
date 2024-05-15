<?php

namespace App\Exception;

use Exception;

class NotFoundTradeException extends \Exception
{
    public function __construct($message = null, $code = 0, Exception $previous = null)
    {
        if (is_null($message)) {
            $message = 'Трейда не существует';
        }

        parent::__construct($message, $code, $previous);
    }
}
