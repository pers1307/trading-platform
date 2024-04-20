<?php

namespace App\Exception;

use Exception;

class UnknownStatusException extends \Exception
{
    public function __construct($message = null, $code = 0, Exception $previous = null)
    {
        if (is_null($message)) {
            $message = 'Передан неизвестный статус трейда';
        }

        parent::__construct($message, $code, $previous);
    }
}
