<?php

namespace app\Models;

use core\App;
use core\Cookies;
use core\Models\BaseModel;

class UserModel extends BaseModel
{
    public string $uuid = '';
    public string $username = '';
    public ?string $name = '';
    public ?string $phone = '';
    public string $password = '';
    public ?string $bio = '';
    public ?string $profilePictureId = '';
    public string|array|false|null $subjects = []; // JSON
    public string $email = '';
    public string|false|array|null $info = ''; // JSON
    public bool $isAdmin = false;
    public string $created_at = '';
    public string $updated_at = '';

    public function __construct(array $data = [])
    {
        parent::loadData($data);
        $this->decodeInfo();
    }

    public static function deleteUserFromDB(string $id): void
    {
        $res = App::$app->database->delete('users', ['id' => $id]);
        App::$app->response->sendJson(['success' => $res], true);
    }

    public function setCookies(): void
    {
        $sessionID = App::$app->session->generateSessionID();
        Cookies::setCookie('id', $this->uuid);
        Cookies::setCookie('sessionID', $sessionID);
        $this->decodeInfo();
        $this->info['sessionID'] = $sessionID;
        App::$app->database->update('users', ['info'], ['info' => json_encode($this->info)], ['id' => $this->uuid]);
    }

    public function isLogin(): bool
    {
        return !empty($this->uuid);
    }

    public function decodeInfo(): void
    {
        if (is_string($this->info)) {
            $this->info = json_decode($this->info, true) ?? [];
        }
        if (is_string($this->subjects)) {
            $this->subjects = json_decode($this->subjects, true) ?? [];
        }
    }

    // Data setters
    public function setUuid(string $uuid): UserModel
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function setPhone(string $phone): UserModel
    {
        $this->phone = $phone;
        return $this;
    }

    public function setPassword(string $password): UserModel
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
        return $this;
    }

    public function setName(string $name): UserModel
    {
        $this->name = $name;
        return $this;
    }

    public function setInfo(array|string $info): UserModel
    {
        $this->info = $info;
        return $this;
    }

    public function setSubjects(array|string $subjects): UserModel
    {
        $this->subjects = $subjects;
        return $this;
    }

    public function setEmail(string $email): UserModel
    {
        $this->email = $email;
        return $this;
    }

    public function setProfilePictureId(string $profilePictureId): UserModel
    {
        $this->profilePictureId = $profilePictureId;
        return $this;
    }

    public function setBio(string $bio): UserModel
    {
        $this->bio = $bio;
        return $this;
    }

    public function setUsername(string $username): UserModel
    {
        $this->username = $username;
        return $this;
    }

    public function isBasicDataSet(): bool
    {
        return !empty($this->uuid) && !empty($this->password) && !empty($this->email) && !empty($this->username);
    }

    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }
}