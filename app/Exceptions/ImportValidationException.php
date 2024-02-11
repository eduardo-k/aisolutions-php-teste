<?php

namespace App\Exceptions;

use Exception;

class ImportValidationException extends Exception
{
    public function __construct(array $message)
    {
        parent::__construct(serialize($message));
    }
}
