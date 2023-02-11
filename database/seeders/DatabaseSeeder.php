<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $check = User::auth([
            "email" => "admin3345.2@gmail.com",
            "password" => "admin1233"
        ]);
        dd($check);
        // $users = collect([
        //     [
        //         "name" => "PPI",
        //         "email" => "ppi303.2@gmail.com",
        //         "password" => bcrypt("ppi123"),
        //         "type" => "user",
        //     ],
        //     [
        //         "name" => "MUTU",
        //         "email" => "mutu12332.2@gmail.com",
        //         "password" => bcrypt("mutu23"),
        //         "type" => "user",
        //     ],
        //     [
        //         "name" => "ADMIN",
        //         "email" => "admin3345.2@gmail.com",
        //         "password" => bcrypt("admin1233"),
        //         "type" => "user",
        //     ],
        // ]);
        // $public = Storage::disk('public');

        // if ($public->exists('users/')) {
        //     $public->put('users/users.json', $users->toJson(JSON_PRETTY_PRINT));
        // } else {
        //     $public->makeDirectory('users');
        //     $public->put('users/users.json', $users->toJson(JSON_PRETTY_PRINT));
        // }
    }
}
