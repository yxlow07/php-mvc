<?php

namespace app\Middleware;

use app\Models\UserModel;
use core\App;
use core\Middleware\BaseMiddleware;

class GuestMiddleware extends BaseMiddleware
{

    public function execute()
    {
        if (isset($_SESSION['user']) && App::$app->session->get('user') instanceof UserModel):
            App::$app->session->setFlashMessage('error', 'Already logged in!');
            redirect();
        else:
            return true;
        endif;
    }

    public function next()
    {
        // TODO: Implement next() method.
    }
}