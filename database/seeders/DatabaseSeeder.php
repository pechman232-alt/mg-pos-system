<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Database\Seeders\MasterData\UserRoleSeeder;
use Database\Seeders\MasterData\UserSeeder;
use Database\Seeders\MasterData\BranchSeeder;
use Database\Seeders\MasterData\PositionSeeder;
use Database\Seeders\MasterData\DepartmentSeeder;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
        $this->call(BranchSeeder::class);
        $this->call(PositionSeeder::class);
        $this->call(DepartmentSeeder::class);
        $this->call(UserRoleSeeder::class);
        $this->call(UserSeeder::class);
    }
}
