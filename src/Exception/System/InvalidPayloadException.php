<?php

namespace App\Exception\System;

use Exception;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

class InvalidPayloadException extends Exception
{
    public function __construct(
        ConstraintViolationListInterface|string $violationList,
        int $code = 400,
        Throwable|null $previous = null
    ) {
        if ($violationList instanceof ConstraintViolationListInterface) {
            $messages = [];
            foreach ($violationList as $violation) {
                $messages[] = $violation->getMessage() . ' ' . $violation->getPropertyPath();
            }

            parent::__construct(implode(',', $messages), $code, $previous);
        } else {
            parent::__construct($violationList, $code, $previous);
        }
    }
}
