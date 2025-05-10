<?php

namespace App\Exception\System\Notification\Email;

use App\Exception\System\SystemException;
use Exception;

class EmailSendingFailedException extends SystemException
{
    public function __construct(string $message = 'Email sending failed', int $code = 500, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
