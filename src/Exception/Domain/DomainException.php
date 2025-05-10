<?php

namespace App\Exception\Domain;

use Exception;

class DomainException extends Exception
{
    public function __construct(string $message = 'Domain Exception', int $code = 500, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
