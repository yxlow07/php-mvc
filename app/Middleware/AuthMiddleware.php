<?php

namespace app\Middleware;

use app\Models\AdminModel;
use app\Models\LoginModel;
use app\Models\UserModel;
use core\App;
use core\Cookies;
use core\Exceptions\MiddlewareException;

class AuthMiddleware
{
    public function setUser(): void
    {
        App::$app->user = App::$app->session->get('user', true);
        App::$app->loggedIn = $this->execute() ? App::$app->user->isLogin() : false;
    }

    public function loginWithCookies(): void
    {
        if ($this->execute()) return;

        $id = Cookies::getCookie('id');
        $sessionId = Cookies::getCookie('sessionID');

        if (is_null($id) || is_null($sessionId)) return;

        // Session ID and user ID are set, can check from database
        /** @var UserModel $user */
        $user = LoginModel::getUserFromDB($id);

        if (!$user) {
            Cookies::unsetCookies(['sessionID', 'id']);
            return;
        }

        if ($user->info['sessionID'] !== $sessionId) {
            Cookies::unsetCookies(['sessionID', 'id']);
            return;
        }

        LoginModel::setNewUpdatedUserData($id);
    }

    public static function execute(): bool
    {
        return App::$app->user instanceof UserModel;
    }
}