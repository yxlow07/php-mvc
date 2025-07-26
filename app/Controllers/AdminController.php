<?php

namespace app\Controllers;

use app\Exceptions\MethodNotAllowedException;
use app\Exceptions\UserNotFoundException;
use app\Models\AdminModel;
use app\Models\AnnouncementModel;
use app\Models\LoginModel;
use app\Models\ProfileModel;
use app\Models\RegisterModel;
use app\Models\TableModel;
use app\Models\UserModel;
use core\App;
use core\Controller;
use core\Database\CSVDatabase;
use core\Database\Generator;
use core\Exceptions\ViewNotFoundException;
use core\Filesystem;
use core\Models\BaseModel;
use core\Models\ValidationModel;
use core\Router\Request;
use core\Router\Response;
use core\View;

class AdminController extends Controller
{
    public function render(string $view, array $params = []): void
    {
        echo View::make(['/views/admin/'])->renderView($view, $params);
    }

    public function list_users(): void
    {
        $users = (array) App::$app->database->findAll('users');

        $this->render('users', ['users' => $users]);
    }

    public function createUsers(): void
    {
        $model = new RegisterModel(App::$app->request->data());

        if (App::$app->request->isMethod('post')) {
            if ($model->validate() && $model->verifyValidUsername() && $model->verifyNoDuplicate() && $model->registerUser()) {
                App::$app->session->setFlashMessage('success', 'Registered new user successfully!');
                redirect('/crud_users');
            }
        }

        echo View::make()->renderView('register', ['model' => $model]);
    }

    /**
     * @throws UserNotFoundException|MethodNotAllowedException
     */
    public function crud_users($id, $action): void
    {
        $data = (array) LoginModel::getUserFromDB($id);

        match ($action) {
            BaseModel::READ => '',
            BaseModel::UPDATE => $this->editUser($data),
            BaseModel::DELETE => UserModel::deleteUserFromDB($id),
            default => $data = BaseModel::UNDEFINED,
        };

        if (isset($data[0]) && $data[0] === false) {
            throw new UserNotFoundException();
        }

        if ($data === BaseModel::UNDEFINED) {
            throw new MethodNotAllowedException();
        }

        $this->render('user_profile', ['data' => $data, 'action' => $action]);
    }

    private function editUser($data): void
    {
        $model = new ProfileModel($data);

        if (App::$app->request->isMethod('post')) {
            $model = new ProfileModel(App::$app->request->data());

            if ($model->validate() && $model->verifyNoDuplicate($data) && $model->updateDatabase($data)) {
                App::$app->session->setFlashMessage('success', 'Updated successfully!');
            }
        }

        $this->render('edit_profile', ['model' => $model, 'isAdmin' => true]);
        exit;
    }

    public function uploadUsers(): void
    {
        $uploadResults = [];

        if (App::$app->request->isMethod('post') && isset($_FILES['csv']) && $_FILES['csv']['error'] == UPLOAD_ERR_OK) {
            $csv = file($_FILES['csv']['tmp_name'], FILE_IGNORE_NEW_LINES);
            $fail = 0;
            foreach ($csv as $line) {
                $data = str_getcsv($line);
                $userModel = new UserModel();
                $i = 0;
                $userModel->setUuid($data[$i++])->setName($data[$i++] ?? 'Anonymous')->setPhone($data[$i++] ?? '+60000000000')
                    ->setPassword($data[$i++] ?? 'abc123')->setInfo($data[$i++] ?? '[]');

                if ($userModel->isBasicDataSet()) {
                    try {
                        if ((new RegisterModel())->registerUser($userModel)) {
                            $uploadResults[] = "Successfully added record for user - $data[0]";
                        } else {
                            $uploadResults[] = "Fail to add record for user - $data[0]";
                            $fail++;
                        }
                    } catch (\Exception $exception) {
                        $uploadResults[] = "Fail to add record for user - $data[0]";
                        $fail++;
                    }
                }
            }
            App::$app->session->setFlashMessage($fail == 0 ? 'success' : 'error', 'CSV Uploaded!');
        }

        $this->render('upload', ['results' => $uploadResults, 'subject' => 'Users']);
    }

    public function add_admin(): void
    {
        $model = new RegisterModel(App::$app->request->data());

        if (App::$app->request->isMethod('post')) {
            if ($model->validate($this->add_admin_rules()) && $model->verifyNoDuplicate()) {
                $adminModel = new UserModel();
                $adminModel->uuid = App::$app->session->generateUUID('users');
                $adminModel->username = $model->username;
                $adminModel->email = $model->email;
                $adminModel->password = password_hash($model->password, PASSWORD_BCRYPT);
                $adminModel->isAdmin = true;

                if ($model->registerUser($adminModel)) {
                    App::$app->session->setFlashMessage('success', 'Successfully created new admin account');
                    redirect('/crud_users');
                } else {
                    App::$app->session->setFlashMessage('error', 'Failed to create new admin account');
                }
            }
        }

        $this->render('add_admin', ['model' => $model, 'isAdmin' => true]);
    }

    public function add_admin_rules(): array
    {
        return [
            'username' => [ValidationModel::RULE_REQUIRED, [ValidationModel::RULE_MIN, 'min' => 3], [ValidationModel::RULE_MAX, 'max' => 15]],
            'email' => [ValidationModel::RULE_REQUIRED, [ValidationModel::RULE_EMAIL]],
            'password' => [ValidationModel::RULE_REQUIRED, [ValidationModel::RULE_MIN, 'min' => 2], [ValidationModel::RULE_MAX, 'max' => 15]], // TODO: Update for prod
            'confirm' => [ValidationModel::RULE_REQUIRED, [ValidationModel::RULE_MATCH, 'match' => 'password', 'matchMsg' => 'must match with password']],
        ];
    }

    public function find_user(): void
    {
        $data = [];

        if (App::$app->request->isMethod('post')) {
            $query = '%' . App::$app->request->data()['query'] . '%';
            $data = App::$app->database->findAll('users', conditions: ['uuid' => $query, 'username'=> $query, 'email'=> $query], isSearch: true);
            App::$app->response->sendJson($data, true);
        }

        $this->render('search', ['users' => $data]);
    }
}