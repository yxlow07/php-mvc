<?php

namespace app\Models;

use core\App;
use core\Models\ValidationModel;

class RegisterModel extends ValidationModel
{
    public string $uuid = '';
    public string $username = '';
    public string $email = '';
    public string $password = '';
    public string $confirm = '';
    public string $created_at = '';
    public string $updated_at = '';

    public function __construct(array $data = [])
    {
        parent::loadData($data);
    }

    public function rules(): array
    {
        return [
            'username' => [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 3], [self::RULE_MAX, 'max' => 15]],
            'email' => [self::RULE_REQUIRED, [self::RULE_EMAIL]],
            'password' => [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 2], [self::RULE_MAX, 'max' => 15]], // TODO: Update for prod
            'confirm' => [self::RULE_REQUIRED, [self::RULE_MATCH, 'match' => 'password', 'matchMsg' => 'must match with password']],
        ];
    }

    public function fieldNames(): array
    {
        return [
            'username' => 'username',
            'password' => 'Password',
            'confirm' => 'Confirm password',
            'email' => 'Email',
        ];
    }

    public function registerUser(?UserModel $userModel = null): bool
    {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
        $this->uuid = App::$app->session->generateUUID('users');
        return App::$app->database->insert('users', ['uuid', 'username', 'email', 'password', 'isAdmin'], is_null($userModel) ? $this : $userModel);
    }

    public function verifyNoDuplicate(): bool
    {
        $usernameCheck = self::checkDatabaseForDuplicates($this->username);
        if (!$usernameCheck) {
            $this->addError(false, 'username', self::RULE_UNIQUE);
        }
        $emailCheck = self::checkDatabaseForDuplicates($this->email, 'email');
        if (!$emailCheck) {
            $this->addError(false, 'email', self::RULE_UNIQUE);
        }
        return $usernameCheck && $emailCheck;
    }

    public function verifyValidUsername(): bool
    {
        $check = preg_match('/^[A-Za-z0-9_]+$/', $this->username);
        if (!$check) {
            $this->addError(false, 'username', self::RULE_MATCH, ['match', 'must have only letters, numbers and underscores']);
        }
        return $check;
    }

    /**
     * @param string $value
     * @param string $field The default is 'username', but can be 'email' or any other field
     * @return bool If a user exists, then return false
     */
    public static function checkDatabaseForDuplicates(string $value, string $field = 'username'): bool
    {
        $user = App::$app->database->findOne('users', [$field => $value], class: UserModel::class);
        if ($user instanceof UserModel) {
            return false;
        } else {
            return true;
        }
    }
}