<?php

namespace app\Models;

use core\App;
use core\Models\ValidationModel;

class ProfileModel extends ValidationModel
{
    const UPDATE_PROFILE = 1;
    const UPDATE_PASSWORD = 2;

    public string $uuid = '';
    public string $username = '';
    public ?string $name = '';
    public ?string $phone = '';
    public string $password = '';
    public ?string $bio = '';
    public ?string $profilePictureId = '';
    public string|array|false|null $subjects = [];
    public string $email = '';
    public string|false|array|null $info = '';
    public bool $isAdmin = false;
    public string $created_at = '';
    public string $updated_at = '';
    public int $type = 1;

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
        $oldId = $this->getOldData($old_data, 'uuid', App::$app->user);
        if ($oldId == $this->uuid) {
            return true;
        }
        $check = RegisterModel::checkDatabaseForDuplicates($this->uuid);
        if (!$check) {
            $this->addError(false, 'uuid', self::RULE_UNIQUE);
        }
        return $check;
    }

    public function updateDatabase(array $old_data = []): bool
    {
        $oldId = $this->getOldData($old_data, 'uuid', App::$app->user);

        // Hash password only if changed
        if (!empty($this->password) && $this->password !== App::$app->user->password) {
            $this->password = password_hash($this->password, PASSWORD_BCRYPT);
        } else {
            $this->password = App::$app->user->password;
        }

        if (empty($this->info)) {
            $this->info = '{}';
        }
        if (empty($this->subjects)) {
            $this->subjects = '{}';
        }

        if (is_array($this->info)) {
            $this->info = json_encode($this->info);
        }
        if (is_array($this->subjects)) {
            $this->subjects = json_encode($this->subjects);
        }

        return App::$app->database->update(
            'users',
            ['uuid', 'username', 'name', 'phone', 'password', 'bio', 'profilePictureId', 'subjects', 'email', 'info', 'isAdmin', 'updated_at'],
            $this,
            ['uuid' => $oldId]
        );
    }

    private function getOldData(array $old_data, string $toFind, object|string $fallback)
    {
        return $old_data[$toFind] ?? (is_object($fallback) ? $fallback->{$toFind} : $fallback);
    }
}