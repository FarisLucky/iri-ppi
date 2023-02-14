<?php

namespace App\Extensions;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Hash;

class DocumentUserProvider implements UserProvider
{
    private $model;

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials)) {
            return;
        }

        $user = $this->model->fetchUserByCredentials($credentials);

        return $user;
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        $check = ($credentials["email"] == $user->getAuthIdentifier() &&
            Hash::check($credentials["password"], $user->getAuthPassword()));

        return $check;
    }

    public function retrieveById($identifier)
    {
        if (empty($identifier)) {
            return;
        }

        $user = $this->model->fetchUserById($identifier);
        // dd($user);
        return $user;
    }

    public function retrieveByToken($identifier, $token)
    {
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
    }
}
