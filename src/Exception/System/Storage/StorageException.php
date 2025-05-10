<?php

namespace App\Exception\System\Storage;

use App\Exception\System\SystemException;
use Exception;

class StorageException extends SystemException
{
    public function __construct(
        string $message = 'Storage operation failed',
        int $code = 500,
        Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
