<?php

namespace app\Controllers;

use app\Middleware\AuthMiddleware;
use app\Models\LoginModel;
use app\Models\ProfileModel;
use app\Models\TableModel;
use app\Models\TablesModel;
use app\Models\UserModel;
use core\App;
use core\Controller;
use core\Database\CSVDatabase;
use core\Database\Database;
use core\Models\ValidationModel;
use core\Router\Request;
use core\Router\Response;
use core\View;

class UserController extends Controller
{
    public function renderHome(): void
    {
        echo View::make()->renderView('index', ['nav' => App::$app->nav]);
    }

    public static function navItems(): void
    {
        $navItems = [
            'user' => [
                '/profile' => ['person', 'Profile'],
            ],
            'admin' => [
                '/add_admin' => [['badge', 'add'], 'Add Admin', true],
                '/crud_users' => [['group', 'edit'], 'Edit Users', true],
                '/find_user' => [['group', 'search'], 'Find User Record', true],
            ],
            'general' => [
                '/' => ['home', 'Homepage'],
            ],
            'end' => [
                '/logout' => ['logout', 'Logout'],
            ],
            'guest' => [
                '/login' => ['login', 'Login'],
                '/register' => ['person_add', 'Register'],
            ],
        ];


        $nav = [
            ...$navItems['general'],
            ...(AuthMiddleware::execute() ? (App::$app->user instanceof UserModel ? $navItems['user'] + $navItems['end'] : $navItems['admin']+ $navItems['end']) : $navItems['guest']),
        ];

        App::$app->nav = $nav;
    }

    public function profilePage(): void
    {
        $model = new ProfileModel(App::$app->request->data());

        if (App::$app->request->isMethod('post')) {
            if ($model->type == ProfileModel::UPDATE_PROFILE) {
                $this->handleUpdateProfile($model);
            } else {
//                $this->handleUpdatePassword($model);
            }
        }
        $model = App::$app->user;
        echo View::make()->renderView('profile', ['model' => $model]);
    }

    private function handleUpdateProfile(ProfileModel $model): void
    {
        if ($model->validate() && $model->verifyNoDuplicate() && $model->updateDatabase()) {
            App::$app->session->setFlashMessage('success', 'Update successfully');
            LoginModel::setNewUpdatedUserData($model->id);
        }
    }
}