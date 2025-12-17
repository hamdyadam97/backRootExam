<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;

use Database\Seeders\CreateUsersSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CreateUsersSeeder::class);
    }
}
