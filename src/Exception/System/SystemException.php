<?php

namespace App\Exception\System;

use Exception;

class SystemException extends Exception
{
    public function __construct(string $message = 'System Exception', int $code = 500, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
