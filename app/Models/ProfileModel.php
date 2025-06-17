<?php

namespace app\Models;

use core\App;
use core\Models\ValidationModel;

class ProfileModel extends ValidationModel
{
    // TODO: Change to new app
    const UPDATE_PROFILE = 1;
    const UPDATE_PASSWORD = 2;

    public string $id = '';
    public string $name = '';
    public string $class = '';
    public string $phone = '';
    public string $password = '';
    public string|false|array $info = ''; // JSON
    public bool $isAdmin = false;
    public string $created_at = '';
    public string $updated_at = '';
    public int $type = 1; // TODO: Change to csrf token

    public function __construct($data = [])
    {
        if (empty($data)) {
            $data = (array) App::$app->user;
        }

        parent::loadData($data);
    }

    public function rules(): array
    {
        $rules = (new RegisterModel())->rules();
        array_pop($rules); array_pop($rules);
        return $rules;
    }

    public function fieldNames(): array
    {
        return (new RegisterModel())->fieldNames();
    }

    public function verifyNoDuplicate(array $old_data = []): bool
    {
        $oldId = $this->getOldData($old_data, 'id', App::$app->user);
        if ($oldId == $this->id) {
            return true;
        }
        $check = RegisterModel::checkDatabaseForDuplicates($this->id);
        if (!$check) {
            $this->addError(false, 'id', self::RULE_UNIQUE);
        }
        return $check;
    }

    public function updateDatabase(array $old_data = []): bool
    {
        $oldId = $this->getOldData($old_data, 'id', App::$app->user);
        if (!empty($this->password)) {
            $this->password = password_hash($this->password, PASSWORD_BCRYPT);
        } else {
            $this->password = App::$app->user->password;
        }
        $this->info = json_encode($this->info);
        return App::$app->database->update('users', ['id', 'name', 'class', 'phone', 'password', 'info'], $this, ['id' => $oldId]);
    }

    public function checkPassword(): bool
    {
        $check = password_verify($this->kLMurid, App::$app->user->kLMurid);
        if (!$check) {
            $this->addError(false, 'kLMurid', self::RULE_MATCH, ['match', 'is incorrect']);
        }
        return $check;
    }

    private function getOldData(array $old_data, string $toFind, object|string $fallback)
    {
        return $old_data[$toFind] ?? (is_object($fallback) ? $fallback->{$toFind} : $fallback);
    }
}