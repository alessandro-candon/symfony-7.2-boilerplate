<?php

namespace App\Exception\System;

use Exception;

class BadOrderParameterException extends SystemException
{
    public function __construct(string $message = 'Bad order parameter', int $code = 400, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
