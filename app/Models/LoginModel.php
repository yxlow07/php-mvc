<?php

namespace app\Models;

use core\App;
use core\Models\ValidationModel;

class LoginModel extends ValidationModel
{
    public string $username = '';
    public string $password = '';
    public bool $rememberMe = false;
    public function __construct(array $data)
    {
        parent::loadData($data);
    }

    public function rules(): array
    {
        return [
            'username' => [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 3]], // TODO: Change to 5 for production
            'password' => [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 3]],
            'rememberMe' => []
        ];
    }

    public function fieldNames(): array
    {
        return [
            'username' => 'Username',
            'password' => 'Password'
        ];
    }

    public function verifyUser(): bool
    {
        /** @var UserModel|AdminModel $user */
        $user = self::getUserFromDB($this->username);

        if (!$user) {
            $user = self::getAdminFromDB($this->username);
            if (!$user) {
                $this->addError(false, 'username', self::RULE_MATCH, ['match', 'must be a valid existing username']);
                return false;
            }
        }

        $checkedPassword = $user->password ?? $user->passwordAdmin;

        if (!password_verify($this->password, $checkedPassword)) {
            $this->addError(false, 'password', self::RULE_MATCH, ['match', 'is incorrect']);
            return false;
        }

        App::$app->user = $user;

        return true;
    }

    public static function getUserFromDB(string $username): UserModel|false
    {
        /** @var UserModel|false $user */
        $user = App::$app->database->findOne('users', conditions: ['username' => $username], class: UserModel::class);

        return $user;
    }

    public static function getAdminFromDB(string $username)
    {
        return App::$app->database->findOne('users', conditions: ['username' => $username, 'isAdmin' => true], class: AdminModel::class);
    }

    public static function setNewUpdatedUserData(string $uuid): void
    {
        $user = self::getUserFromDB($uuid);

        App::$app->user = $user;
        App::$app->session->set('user', App::$app->user);
    }
}