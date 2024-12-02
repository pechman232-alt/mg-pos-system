<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Database\Seeders\MasterData\UserRoleSeeder;
use Database\Seeders\MasterData\UserSeeder;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
        $this->call(UserRoleSeeder::class);
        $this->call(UserSeeder::class);
    }
}
