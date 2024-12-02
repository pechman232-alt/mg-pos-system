<?php

namespace Database\Seeders\MasterData;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\UsersModel;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $this->command->info(" /************** Creating User **************/ ");
            $user = [
                'full_name' => 'Chouhong Va',
                'username' => "chouhong",
                'user_id' => "999999",
                'gender' => "Male",
                'email' => "chouhong123@gmail.com",
                'phone_number' => "086625602",
                'branch_id' => 1,
                'department_id' => 1,
                'position_id' => 1,
                'start_work_date' => Carbon::now(),
                'password' => Hash::make("123123123"),
                'role_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            UsersModel::create($user);
            $this->command->info(" /************** User Created **************/ ");
        } catch (\Exception $e) {
            $this->command->error("Error: " . $e->getMessage());
        }
    }
}
