<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Guest;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Default credentials
        \App\Models\User::insert([
            [
                'branch_id'=>1,
                'username'=>'admin',
                'name' => 'admin',
                'email' => 'midone@left4code.com',
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'gender' => 'male',
                'photo' => 'default.png',
                'contact_no' => '0115 631 032',
                'address' => 'colombo',
                'layout'=>'side-menu',
                'theme'=>'light',
                'currency_id'=>1,
                'user_group_id'=>1,
                'status'=>1,
                'remember_token' => Str::random(10)
            ]
        ]);

        // Fake users
       // User::factory()->times(9)->create();


    }
}
