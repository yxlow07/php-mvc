<?php

namespace app\Exceptions;

use core\Exceptions\BaseException;

class MethodNotAllowedException extends BaseException
{
    protected $message = 'Method is not allowed on this object';
    protected $code = 400;
}