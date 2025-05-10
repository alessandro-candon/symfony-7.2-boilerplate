<?php

namespace App\Exception\System;

use Exception;

class BadFilterDataException extends SystemException
{
    public function __construct(string $message = 'Bad filter', int $code = 400, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
