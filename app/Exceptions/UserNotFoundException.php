<?php

namespace app\Exceptions;

use core\Exceptions\BaseException;

class UserNotFoundException extends BaseException
{
    protected $message = "User is not found with this ID";
    protected $code = 404;
}