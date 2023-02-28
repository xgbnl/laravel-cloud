<?php

namespace Xgbnl\Cloud\Exceptions;

use http\Exception\RuntimeException;
use Throwable;

class FailedResolveException extends RuntimeException
{
    public function __construct(string $message = "", int $code = 500, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}