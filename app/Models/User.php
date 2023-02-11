<?php

namespace App\Models;

use Exception;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function authFile()
    {
        $file = Storage::disk('public')->get('users/users.json');
        return json_decode($file, true);
    }

    public static function auth($params)
    {
        $users = self::authFile();

        $email = collect($users)->filter(function ($user) use ($params) {
            return $user["email"] == $params['email'];
        })->shift();

        if (is_null($email)) {
            throw new Exception("EMAIL ATAU PASSWORD TIDAK DITEMUKAN");
        }
        if (Hash::check($params["password"], $email["password"])) {
            return true;
        }

        return false;
    }
}
