<?php

namespace Xgbnl\Cloud\Exceptions;

use RuntimeException;
use Throwable;

class Exception extends RuntimeException implements Throwable
{
    protected int $status;

    protected string $error;

    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        $this->status = $code;
        $this->error = $message;
        parent::__construct($message, $code, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->status;
    }
}