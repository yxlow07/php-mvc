<?php

namespace core\Exceptions;

class BadBindingException extends Exception
{
    protected $message = 'Bad binding to container';
    protected $code = 500;
}