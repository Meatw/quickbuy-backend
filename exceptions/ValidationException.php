<?php

class ValidationException extends Exception
{
    public function __construct($message = "Validation failed", $code = 422, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
