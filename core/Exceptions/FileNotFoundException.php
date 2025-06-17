<?php

namespace core\Exceptions;

use core\Exceptions\BaseException;

class FileNotFoundException extends BaseException
{
    protected $message = "Exception occurred, file not found!";
    protected $code = 400;
}