<?php

namespace App\Services;

use App\Services\Contracts\DocumentServiceInterface;
use Illuminate\Support\Facades\Storage;

class DocumentCollection implements DocumentServiceInterface
{
    private $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function find($criteria)
    {
        $users = $this->data();

        $email = collect($users)->filter(function ($user) use ($criteria) {
            return $user["email"] == $criteria['email'];
        });

        return $email->shift();
    }

    public function data()
    {
        $this->path = Storage::disk('public')->get('users/users.json');

        return json_decode($this->path, true);
    }
}
