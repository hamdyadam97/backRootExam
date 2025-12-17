<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\User;

class CreateUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
               'first_name'=>'Admin',
               'last_name'=>'Admin',
               'mobile'=>'8000571013',
               'role_type'=>1,
               'status'=>1,
               'password'=> '$2y$10$Pf7.BmW5tNiNnd8D5gNgsO.Y0MQuXWgZHavnuOd1yVYn8WKDessc6',
            ],

        ];

        foreach ($users as $key => $user) {
            User::create($user);
        }
    }
}
