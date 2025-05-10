<?php

namespace App\Exception\System;

use Exception;

class PaginationException extends SystemException
{
    public function __construct(string $message = 'Invalid pagination', int $code = 400, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
