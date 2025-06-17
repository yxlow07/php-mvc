<?php

namespace app\Models;

use core\App;
use core\Models\ValidationModel;

class AdminModel extends ValidationModel
{
    public string $idAdmin = '';
    public string $adminName = '';
    public string $password = '';
    public string $created_at = '';
    public string $updated_at = '';
    public bool $isAdmin = true;

    public function __construct(array $data = [])
    {
        parent::loadData($data);
    }

    public function newAdminRules(): array
    {
        return [
            'idAdmin' => [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 5]],
            'adminName' => [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 5]],
            'password' => [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 3]],
        ];
    }

    public function rules(): array
    {
        return [
            'id' => [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 5]],
            'password' => [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 5]],
            'rememberMe' => []
        ];
    }

    public function fieldNames(): array
    {
        return [
            'id' => 'Admin ID ',
            'password' => 'Password',
            'idAdmin' => 'Admin ID ',
            'adminName' => 'Admin Name',
        ];
    }

    public function isLogin(): bool
    {
        return !empty($this->idAdmin);
    }

    public function verifyNoDuplicate(): bool
    {
        $check = $this->checkForDuplicates($this->idAdmin);

        if (!$check) {
            $this->addError(false, 'idAdmin', self::RULE_UNIQUE);
        }

        return $check;
    }

    public function updateDatabase(): bool
    {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
        $this->permissions = json_encode($this->permissions);
        
        return App::$app->database->insert('admin', ['idAdmin', 'adminName', 'password', 'permissions'], $this);
    }

    public function checkForDuplicates(string $idAdmin): bool
    {
        $admin = App::$app->database->findOne('admin', ['idAdmin' => $idAdmin], class: AdminModel::class);

        if ($admin instanceof AdminModel) {
            return false;
        } else {
            return true;
        }
    }
}