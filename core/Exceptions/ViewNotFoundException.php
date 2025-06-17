<?php

namespace core\Exceptions;

use core\App;
use Throwable;

class ViewNotFoundException extends BaseException
{
    public function __construct(string $message = "View file is not found", int $code = 400, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        parent::setCode($code);
    }
}