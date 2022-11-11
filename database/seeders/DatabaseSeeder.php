<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;



//use App\Models\UserGroupManagement;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {


        $userseeder = new UserSeeder();

    
       // UserGroupManagement::create(['module_name'=> 'Additional_Service','user_group_id'=> '1','module_sts'=>'1','last_update_time'=>'1000-01-01 00:00:00']);



        $userseeder->run();
    }
}
