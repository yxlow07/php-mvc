<?php

namespace app\Middleware;

use app\Models\UserModel;
use core\App;
use core\Middleware\BaseMiddleware;

class GuestMiddleware extends BaseMiddleware
{

    public function execute()
    {
        if (!isset($_SESSION['user']) && !(App::$app->session->get('user') instanceof UserModel)):
            return true;
        else:
            App::$app->session->setFlashMessage('error', 'Already logged in!');
            redirect();
        endif;
    }

    public function next()
    {
        // TODO: Implement next() method.
    }
}