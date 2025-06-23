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
                '/profile' => ['user', 'Profile'],
            ],
            'admin' => [
                '/add_admin' => [['user-tie', 'plus'], 'Add Admin', true],
                '/crud_users' => [['users', 'pencil'], 'Edit Users', true],
                '/find_user' => [['users', 'magnifying-glass'], 'Find User Record', true],
            ],
            'general' => [
                '/' => ['house', 'Homepage'],
            ],
            'authenticated' => [
                '/logout' => ['right-from-bracket', 'Logout'],
            ],
            'guest' => [
                '/login' => ['right-to-bracket', 'Login'],
                '/register' => ['user-plus', 'Register'],
            ],
        ];


        $nav = [...$navItems['general']];

        if (AuthMiddleware::execute()) {
            $user = App::$app->user;

            if ($user instanceof UserModel && $user->isAdmin()) {
                $nav = [...$nav, ...$navItems['admin']];
            }

            $nav = [...$nav, ...$navItems['user'], ...$navItems['authenticated']];
        } else {
            $nav = [...$nav, ...$navItems['guest']];
        }

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

    public function uploadPicture(): void
    {
        $data = App::$app->request->files();

        if (isset($data['profile_picture'])) {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://api.imgur.com/3/image',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => [
                    'image' => new \CurlFile($data['profile_picture']['tmp_name'], $data['profile_picture']['type'], $data['profile_picture']['name'])
                ],
                CURLOPT_HTTPHEADER => [
                    'Authorization: Client-ID '.$_ENV['IMGUR_CLIENT_ID'],
                ],
            ]);

            $response = curl_exec($curl);
            curl_close($curl);
            $response = json_decode($response, true);
            if ($response['status'] == 200) {
                $model = new ProfileModel(App::$app->request->data());
                $model->profilePictureId = $response['data']['link'];

                if ($model->updateDatabase()) {
                    App::$app->session->setFlashMessage('success', 'Profile picture updated successfully');
                    LoginModel::setNewUpdatedUserData($model->uuid);
                    redirect('/profile');
                } else {
                    App::$app->session->setFlashMessage('error', 'Failed to update profile picture');
                }
            } else {
                App::$app->session->setFlashMessage('error', 'Failed to upload image to Imgur');
            }
        }

        App::$app->session->setFlashMessage('error', 'Failed to upload image');
        redirect('/profile');
    }
}