<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class FileService
{
    public $name, $data;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getFile()
    {
        return Storage::disk('public')->get($this->name . $this->ext());
    }

    public function read()
    {
        return collect($this->decode());
    }

    public function decode()
    {
        return json_decode($this->getFile(), true);
    }

    public function ext()
    {
        return '.json';
    }
}
