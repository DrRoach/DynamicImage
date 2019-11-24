<?php

namespace DynamicImage\Exceptions;

use \Exception;

class MissingImageException extends Exception
{
    public function __construct($message, $code = 502, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
