<?php

namespace App\Exceptions;

use Throwable;

class ValidationException extends \Exception
{
    public function __construct(string $message = "Ошибка валидации", int $code = 401, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}