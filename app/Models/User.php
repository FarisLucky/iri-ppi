<?php

namespace App\Models;

use App\Services\DocumentCollection;
use Exception;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class User implements AuthenticatableContract
{
    // use HasFactory, Notifiable;

    private $conn;

    public $id, $email, $password, $type;

    public function __construct(DocumentCollection $conn)
    {
        $this->conn = $conn;
    }

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

    // public static function authFile()
    // {
    //     $file = Storage::disk('public')->get('users/users.json');
    //     return json_decode($file, true);
    // }

    // public static function auth($params)
    // {
    //     $users = self::authFile();

    //     $email = collect($users)->filter(function ($user) use ($params) {
    //         return $user["email"] == $params['email'];
    //     })->shift();

    //     if (is_null($email)) {
    //         throw new Exception("EMAIL ATAU PASSWORD TIDAK DITEMUKAN");
    //     }
    //     if (Hash::check($params["password"], $email["password"])) {
    //         dd($email);
    //         return true;
    //     }

    //     return false;
    // }

    public function fetchUserByCredentials(array $credentials)
    {
        $user = $this->conn->find($credentials);

        if (!is_null($user)) {
            $this->id = $user['id'];
            $this->email = $user['email'];
            $this->type = $user['type'];
            $this->password = $user['password'];
        }
        return $this;
    }

    public function fetchUserById($identifier)
    {
        $criteria = [
            "email" => $identifier
        ];
        $user = $this->conn->find($criteria);

        if (!is_null($user)) {
            $this->id = $user['id'];
            $this->email = $user['email'];
            $this->type = $user['type'];
            $this->password = $user['password'];
        }
        return $this;
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return "email";
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->{$this->getAuthIdentifierName()};
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string  $value
     * @return void
     */
    public function setRememberToken($value)
    {
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
    }
}
