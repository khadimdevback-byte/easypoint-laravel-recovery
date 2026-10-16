<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class PointageForbiddenException extends Exception
{
    public function __construct(string $message = "Vous ne pouvez pas pointer à ce moment-là.", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
