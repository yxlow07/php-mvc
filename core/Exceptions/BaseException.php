<?php

namespace core\Exceptions;

use core\App;
use core\View;
use Throwable;

class BaseException extends \Exception
{

//    public function render($code = 400, $defaultMsg = 'Unhandled exception occurred', $view = ''): string
//    {
//        $msg = $this->getMessage() ?? $defaultMsg;
//        $data = ['message' => $msg, 'trace' => $this->getTraceAsString(), 'code' => $code];
//
//        $this->setCode($code);
//
//        if (property_exists('core\App', 'view')) {
//            return View::make()->renderView($view, $data);
//        } else {
//            return $msg;
//        }
//    }
//
//    public function getView(): string
//    {
//        return $this->view;
//    }
//
    public function setCode(int $code): void
    {
        if ($code !== 200) {
            http_response_code($code);
        }
    }
}