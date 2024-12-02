<?php

namespace Database\Seeders\MasterData;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserRolesModel;
use Carbon\Carbon;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $this->command->info(" /************** Creating Roles **************/ ");
            $roles = [
                [
                    "name" => "Super Admin",
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    "name" => "Admin",
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    "name" => "User Standard",
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            ];
            UserRolesModel::insert($roles);

            $this->command->info(" /************** Roles Created **************/ ");
        } catch (\Exception $e) {
            $this->command->error("Error: " . $e->getMessage());
        }
    }
}
