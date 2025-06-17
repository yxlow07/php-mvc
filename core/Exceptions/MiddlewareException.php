<?php

namespace core\Exceptions;

use core\Exceptions\BaseException;

class MiddlewareException extends BaseException
{
    protected $message = "Exception occurred at the middleware";
    protected $code = 400;
}