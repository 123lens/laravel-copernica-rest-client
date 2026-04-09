<?php

namespace Budgetlens\Copernica\RestClient\Exceptions;

use RuntimeException;

class CopernicaException extends RuntimeException
{
    public function __construct(
        string $message,
        int $code = 0,
        public readonly array $errors = [],
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
