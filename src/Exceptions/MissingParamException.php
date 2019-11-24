<?php

namespace DynamicImage\Exceptions;

use \Exception;

class MissingParamException extends Exception
{
    public function __construct($message, $code = 501, Exception $previous = null) 
    {
        parent::__construct($message, $code, $previous);
    }
}
