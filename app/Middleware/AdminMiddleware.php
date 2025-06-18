<?php

namespace app\Middleware;

use app\Models\UserModel;
use core\App;
use core\Middleware\BaseMiddleware;

class AdminMiddleware extends BaseMiddleware
{

    public function execute()
    {
        if (isset($_SESSION['user']) && (App::$app->session->get('user') instanceof UserModel) && App::$app->session->get('user')->isAdmin()):
            return true;
        else:
            App::$app->session->setFlashMessage('error', 'You cannot view because you are not an admin');
            redirect();
        endif;
    }

    public function next()
    {
        // TODO: Implement next() method.
    }
}