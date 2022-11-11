<?php

namespace Modules\PrimaryModule\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use Modules\PrimaryModule\Models\Cfg_generate_id;
use Modules\PrimaryModule\Models\Cfg_branch;
use Modules\PrimaryModule\Models\Cfg_currency;
use Modules\PrimaryModule\Models\Room_category;
use Modules\PrimaryModule\Models\MealPlan;
use Modules\PrimaryModule\Models\Room_type;
use Modules\PrimaryModule\Models\Room;
use Modules\PrimaryModule\Models\Additional_facilities;
use Modules\PrimaryModule\Models\Season;
use Modules\PrimaryModule\Models\Agent;
use Modules\PrimaryModule\Models\Cfg_module;
use Modules\PrimaryModule\Models\Guest;
use Modules\PrimaryModule\Models\Room_cat_amount;
use Modules\PrimaryModule\Models\UserGroup;
use Modules\PrimaryModule\Models\UserGroupPermission;
use Modules\PrimaryModule\Models\UserPermission;
use Modules\PrimaryModule\Models\UserPermissionsCategory;

class PrimaryModuleDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        // --------------- important ---------------------------------------------------------------------//

        // please do not remove the below seeds which is required by the production

        Cfg_module::create([
            'module_name' => 'PrimaryModule',
            'status' => '1',
            'files_status' => '1',
        ]);
        Cfg_module::create([
            'module_name' => 'Housekeeping',
            'status' => '0',
            'files_status' => '0',
        ]);
        Cfg_module::create([
            'module_name' => 'AdditionalServices',
            'status' => '0',
            'files_status' => '0',
        ]);
        Cfg_module::create([
            'module_name' => 'Inventory',
            'status' => '0',
            'files_status' => '0',
        ]);

        Cfg_generate_id::create([
            'id_year' => 1,
            'id_month' => 1,
        ]);


        Cfg_branch::create([
            'b_name' => 'KND',
        ]);


        // Cfg_currency::create([
        //     'c_symbol' => 'Rs',
        //     'c_name' => 'LKR',
        //     'c_rate' => 1,
        // ]);

        // Cfg_currency::create([
        //     'c_symbol' => '$',
        //     'c_name' => 'USD',
        //     'c_rate' => 0,
        // ]);


        MealPlan::create([
            'mealPlanCode' => 'B/B',
            'mealPlanName' => 'Bed & Breakfast',
            'created_by' => 1,
            'updated_by' => 1,
            'status' => 1,
            'created_at' => '2021-12-09 06:29:35',
            'updated_at' => null
        ]);

        MealPlan::create([
            'mealPlanCode' => 'H/B',
            'mealPlanName' => 'Half Board',
            'created_by' => 1,
            'updated_by' => 1,
            'status' => 1,
            'created_at' => '2021-12-09 06:29:35',
            'updated_at' => null
        ]);

        MealPlan::create([
            'mealPlanCode' => 'F/B',
            'mealPlanName' => 'Full Board',
            'created_by' => 1,
            'updated_by' => 1,
            'status' => 1,
            'created_at' => '2021-12-09 06:29:35',
            'updated_at' => null
        ]);



        Room_category::create(['room_categories_name' => 'single', 'area' => '200', 'max_recident' => '5', 'default_recident' => '2', 'max_adults' => '2', 'max_children' => '3', 'status' => '1', 'created_by' => '1', 'created_at' => '2021-12-09 06:29:35', 'updated_by' => '1','updated_at' => '2021-12-09 06:29:35']);
        Room_category::create(['room_categories_name' => 'double', 'area' => '200', 'max_recident' => '5', 'default_recident' => '2', 'max_adults' => '2', 'max_children' => '3', 'status' => '1', 'created_by' => '1', 'created_at' => '2021-12-09 06:29:35', 'updated_by' => '1','updated_at' => '2021-12-09 06:29:35']);
        Room_category::create(['room_categories_name' => 'Triple', 'area' => '200', 'max_recident' => '5', 'default_recident' => '2', 'max_adults' => '2', 'max_children' => '3', 'status' => '1', 'created_by' => '1', 'created_at' => '2021-12-09 06:29:35', 'updated_by' => '1','updated_at' => '2021-12-09 06:29:35']);


        // these are the permissions for the routes

        UserGroup::create([
            'user_group_name'=>'administrator',
        ]);

        UserPermission::create([
            'permission_code'=>1,
            'permission_name'=>'Settings page access',
            'module_id'=>1,
            'category_id'=>1
        ]);

        UserPermission::create([
            'permission_code'=>2,
            'permission_name'=>'User Permissions',
            'module_id'=>1,
            'category_id'=>2
        ]);

        UserPermission::create([
            'permission_code'=>3,
            'permission_name'=>'Seasons View',
            'module_id'=>1,
            'category_id'=>3
        ]);

        UserPermission::create([
            'permission_code'=>4,
            'permission_name'=>'Agents View',
            'module_id'=>1,
            'category_id'=>3
        ]);

        UserPermission::create([
            'permission_code'=>5,
            'permission_name'=>'Add / Edit Agents',
            'module_id'=>1,
            'category_id'=>3
        ]);


        UserPermission::create([
            'permission_code'=>7,
            'permission_name'=>'Add / Update Meal Plan',
            'module_id'=>1,
            'category_id'=>3
        ]);

        UserPermission::create([
            'permission_code'=>8,
            'permission_name'=>'Room Rates View',
            'module_id'=>1,
            'category_id'=>3
        ]);

        UserPermission::create([
            'permission_code'=>9,
            'permission_name'=>'Add / Edit Room Rate',
            'module_id'=>1,
            'category_id'=>3
        ]);

        UserPermission::create([
            'permission_code'=>10,
            'permission_name'=>'Guest View',
            'module_id'=>1,
            'category_id'=>4
        ]);

        UserPermission::create([
            'permission_code'=>11,
            'permission_name'=>'Add / Edit Guest',
            'module_id'=>1,
            'category_id'=>4
        ]);

        UserPermission::create([
            'permission_code'=>12,
            'permission_name'=>'Room Category View',
            'module_id'=>1,
            'category_id'=>4
        ]);

        UserPermission::create([
            'permission_code'=>13,
            'permission_name'=>'Room Type View',
            'module_id'=>1,
            'category_id'=>4
        ]);


        UserPermission::create([
            'permission_code'=>14,
            'permission_name'=>'Rooms View',
            'module_id'=>1,
            'category_id'=>4
        ]);

        UserPermission::create([
            'permission_code'=>15,
            'permission_name'=>'Add / Edit Room',
            'module_id'=>1,
            'category_id'=>4
        ]);

        UserPermission::create([
            'permission_code'=>16,
            'permission_name'=>'Room Facilities',
            'module_id'=>1,
            'category_id'=>4
        ]);


        UserPermission::create([
            'permission_code'=>17,
            'permission_name'=>'Booking View',
            'module_id'=>1,
            'category_id'=>4
        ]);

        UserPermission::create([
            'permission_code'=>18,
            'permission_name'=>'Add / Edit Booking',
            'module_id'=>1,
            'category_id'=>4
        ]);

        UserPermission::create([
            'permission_code'=>19,
            'permission_name'=>'Room Resevation View',
            'module_id'=>1,
            'category_id'=>4
        ]);

        UserPermission::create([
            'permission_code'=>20,
            'permission_name'=>'Add Reservation',
            'module_id'=>1,
            'category_id'=>4
        ]);

        UserPermission::create([
            'permission_code'=>21,
            'permission_name'=>'Edit Reservation',
            'module_id'=>1,
            'category_id'=>4
        ]);

        UserPermission::create([
            'permission_code'=>22,
            'permission_name'=>'Assign Reservation Guest List',
            'module_id'=>1,
            'category_id'=>4
        ]);


        UserPermission::create([
            'permission_code'=>23,
            'permission_name'=>'Print GRC',
            'module_id'=>1,
            'category_id'=>5
        ]);

        UserPermission::create([
            'permission_code'=>24,
            'permission_name'=>'Print Room BIll',
            'module_id'=>1,
            'category_id'=>5
        ]);


        UserPermission::create([
            'permission_code'=>25,
            'permission_name'=>'Reservation Checkout',
            'module_id'=>1,
            'category_id'=>4
        ]);

        UserPermission::create([
            'permission_code'=>26,
            'permission_name'=>'Daily Forecast View',
            'module_id'=>1,
            'category_id'=>5
        ]);

        UserPermission::create([
            'permission_code'=>27,
            'permission_name'=>'Print Daily Forecast',
            'module_id'=>1,
            'category_id'=>5
        ]);

        UserPermission::create([
            'permission_code'=>28,
            'permission_name'=>'Room Allocation Forecast View',
            'module_id'=>1,
            'category_id'=>5
        ]);

        UserPermission::create([
            'permission_code'=>29,
            'permission_name'=>'Print Room Allocation Forecast',
            'module_id'=>1,
            'category_id'=>5
        ]);

        UserPermission::create([
            'permission_code'=>30,
            'permission_name'=>'Users view',
            'module_id'=>1,
            'category_id'=>2
        ]);

        UserPermission::create([
            'permission_code'=>31,
            'permission_name'=>'User add edit page access',
            'module_id'=>1,
            'category_id'=>2
        ]);
        UserPermission::create([
            'permission_code'=>32,
            'permission_name'=>'Room Check-In',
            'module_id'=>1,
            'category_id'=>4
        ]);
        UserPermission::create([
            'permission_code'=>34,
            'permission_name'=>'Tax Define',
            'module_id'=>1,
            'category_id'=>5
        ]);

        UserGroupPermission::create([
            'user_group_id'=>1,
            'permission_code'=>1,
        ]);

        UserGroupPermission::create([
            'user_group_id'=>1,
            'permission_code'=>2,
        ]);
        

        UserPermissionsCategory::create(['id'=>1, 'category'=>'Configuration']);
        UserPermissionsCategory::create(['id'=>2, 'category'=>'System Access']);
        UserPermissionsCategory::create(['id'=>3, 'category'=>'Hotel Configuration']);
        UserPermissionsCategory::create(['id'=>4, 'category'=>'Rooms']);
        UserPermissionsCategory::create(['id'=>5, 'category'=>'Reports']);

        $cfg_currencies = array(
            array('id' => '1','c_symbol' => 'Rs','c_name' => 'LKR','c_rate' => '1','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '2','c_symbol' => 'AFN','c_name' => 'AFN','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '3','c_symbol' => 'ARS','c_name' => 'ARS','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '4','c_symbol' => 'AWG','c_name' => 'AWG','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '5','c_symbol' => 'AUD','c_name' => 'AUD','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '6','c_symbol' => 'AZN','c_name' => 'AZN','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '7','c_symbol' => 'BSD','c_name' => 'BSD','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '8','c_symbol' => 'BBD','c_name' => 'BBD','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '9','c_symbol' => 'BYR','c_name' => 'BYR','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '10','c_symbol' => 'BZD','c_name' => 'BZD','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '11','c_symbol' => 'BMD','c_name' => 'BMD','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '12','c_symbol' => 'BOB','c_name' => 'BOB','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '13','c_symbol' => 'BAM','c_name' => 'BAM','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '14','c_symbol' => 'BWP','c_name' => 'BWP','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '15','c_symbol' => 'BGN','c_name' => 'BGN','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '16','c_symbol' => 'BRL','c_name' => 'BRL','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '17','c_symbol' => 'BND','c_name' => 'BND','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '18','c_symbol' => 'KHR','c_name' => 'KHR','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '19','c_symbol' => 'CAD','c_name' => 'CAD','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '20','c_symbol' => 'KYD','c_name' => 'KYD','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '21','c_symbol' => 'CLP','c_name' => 'CLP','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '22','c_symbol' => 'CNY','c_name' => 'CNY','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '23','c_symbol' => 'COP','c_name' => 'COP','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '24','c_symbol' => 'CRC','c_name' => 'CRC','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '25','c_symbol' => 'HRK','c_name' => 'HRK','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '26','c_symbol' => 'CUP','c_name' => 'CUP','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '27','c_symbol' => 'CZK','c_name' => 'CZK','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '28','c_symbol' => 'DKK','c_name' => 'DKK','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '29','c_symbol' => 'DOP','c_name' => 'DOP','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '30','c_symbol' => 'XCD','c_name' => 'XCD','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '31','c_symbol' => 'EGP','c_name' => 'EGP','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '32','c_symbol' => 'SVC','c_name' => 'SVC','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '33','c_symbol' => 'EEK','c_name' => 'EEK','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '34','c_symbol' => 'EUR','c_name' => 'EUR','c_rate' => '0.002632','created_at' => '0000-00-00 00:00:00','updated_at' => '2022-05-06 03:37:58'),
            array('id' => '35','c_symbol' => 'FKP','c_name' => 'FKP','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '36','c_symbol' => 'FJD','c_name' => 'FJD','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '37','c_symbol' => 'GHC','c_name' => 'GHC','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '38','c_symbol' => 'GIP','c_name' => 'GIP','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '39','c_symbol' => 'GTQ','c_name' => 'GTQ','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '40','c_symbol' => 'GGP','c_name' => 'GGP','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '41','c_symbol' => 'GYD','c_name' => 'GYD','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '42','c_symbol' => 'HNL','c_name' => 'HNL','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '43','c_symbol' => 'HKD','c_name' => 'HKD','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '44','c_symbol' => 'HUF','c_name' => 'HUF','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '45','c_symbol' => 'ISK','c_name' => 'ISK','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '46','c_symbol' => 'INR','c_name' => 'INR','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '47','c_symbol' => 'IDR','c_name' => 'IDR','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '48','c_symbol' => 'IRR','c_name' => 'IRR','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '49','c_symbol' => 'IMP','c_name' => 'IMP','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '50','c_symbol' => 'ILS','c_name' => 'ILS','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '51','c_symbol' => 'JMD','c_name' => 'JMD','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '52','c_symbol' => 'JPY','c_name' => 'JPY','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '53','c_symbol' => 'JEP','c_name' => 'JEP','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '54','c_symbol' => 'KZT','c_name' => 'KZT','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '55','c_symbol' => 'KPW','c_name' => 'KPW','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '56','c_symbol' => 'KGS','c_name' => 'KGS','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '57','c_symbol' => 'LAK','c_name' => 'LAK','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '58','c_symbol' => 'LVL','c_name' => 'LVL','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '59','c_symbol' => 'LBP','c_name' => 'LBP','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '60','c_symbol' => 'LRD','c_name' => 'LRD','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '61','c_symbol' => 'LTL','c_name' => 'LTL','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '62','c_symbol' => 'MKD','c_name' => 'MKD','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '63','c_symbol' => 'MYR','c_name' => 'MYR','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '64','c_symbol' => 'MUR','c_name' => 'MUR','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '65','c_symbol' => 'MXN','c_name' => 'MXN','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '66','c_symbol' => 'MNT','c_name' => 'MNT','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '67','c_symbol' => 'MZN','c_name' => 'MZN','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '68','c_symbol' => 'NAD','c_name' => 'NAD','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '69','c_symbol' => 'NPR','c_name' => 'NPR','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '70','c_symbol' => 'ANG','c_name' => 'ANG','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '71','c_symbol' => 'NZD','c_name' => 'NZD','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '72','c_symbol' => 'NIO','c_name' => 'NIO','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '73','c_symbol' => 'NGN','c_name' => 'NGN','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '74','c_symbol' => 'NOK','c_name' => 'NOK','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '75','c_symbol' => 'OMR','c_name' => 'OMR','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '76','c_symbol' => 'PKR','c_name' => 'PKR','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '77','c_symbol' => 'PAB','c_name' => 'PAB','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '78','c_symbol' => 'PYG','c_name' => 'PYG','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '79','c_symbol' => 'PEN','c_name' => 'PEN','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '80','c_symbol' => 'PHP','c_name' => 'PHP','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '81','c_symbol' => 'PLN','c_name' => 'PLN','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '82','c_symbol' => 'QAR','c_name' => 'QAR','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '83','c_symbol' => 'RON','c_name' => 'RON','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '84','c_symbol' => 'RUB','c_name' => 'RUB','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '85','c_symbol' => 'SHP','c_name' => 'SHP','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '86','c_symbol' => 'SAR','c_name' => 'SAR','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '87','c_symbol' => 'RSD','c_name' => 'RSD','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '88','c_symbol' => 'SCR','c_name' => 'SCR','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '89','c_symbol' => 'SGD','c_name' => 'SGD','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '90','c_symbol' => 'SBD','c_name' => 'SBD','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '91','c_symbol' => 'SOS','c_name' => 'SOS','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '92','c_symbol' => 'ZAR','c_name' => 'ZAR','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '93','c_symbol' => 'KRW','c_name' => 'KRW','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '94','c_symbol' => 'SEK','c_name' => 'SEK','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '95','c_symbol' => 'CHF','c_name' => 'CHF','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '96','c_symbol' => 'SRD','c_name' => 'SRD','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '97','c_symbol' => 'SYP','c_name' => 'SYP','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '98','c_symbol' => 'TWD','c_name' => 'TWD','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '99','c_symbol' => 'THB','c_name' => 'THB','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '100','c_symbol' => 'TTD','c_name' => 'TTD','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '101','c_symbol' => 'TRY','c_name' => 'TRY','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '102','c_symbol' => 'TRL','c_name' => 'TRL','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '103','c_symbol' => 'TVD','c_name' => 'TVD','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '104','c_symbol' => 'UAH','c_name' => 'UAH','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '105','c_symbol' => 'GBP','c_name' => 'GBP','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '106','c_symbol' => '$','c_name' => 'USD','c_rate' => '0.002774','created_at' => '0000-00-00 00:00:00','updated_at' => '2022-05-06 03:39:17'),
            array('id' => '107','c_symbol' => 'UYU','c_name' => 'UYU','c_rate' => '0.114202','created_at' => '0000-00-00 00:00:00','updated_at' => '2022-05-06 03:41:23'),
            array('id' => '108','c_symbol' => 'UZS','c_name' => 'UZS','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '109','c_symbol' => 'VEF','c_name' => 'VEF','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '110','c_symbol' => 'VND','c_name' => 'VND','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '111','c_symbol' => 'YER','c_name' => 'YER','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'),
            array('id' => '112','c_symbol' => 'ZWD','c_name' => 'ZWD','c_rate' => '0','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00')
          );



          Cfg_currency::insert($cfg_currencies);

        // -------------------------------------------


        //------------------------------------------------------------------------------------------------------

        //----------------------------------------------un wanted stuff start------------------------------------------




        Room_cat_amount::create(['room_type_id' => '1', 'room_categories_id' => '1', 'room_cat_amounts_amount' => '10000']);
        Room_cat_amount::create(['room_type_id' => '1', 'room_categories_id' => '2', 'room_cat_amounts_amount' => '20000']);
        Room_cat_amount::create(['room_type_id' => '1', 'room_categories_id' => '3', 'room_cat_amounts_amount' => '50000']);


        Additional_facilities::create(['room_id' => '1', 'facilities' => '1']);
        Additional_facilities::create(['room_id' => '1', 'facilities' => '5']);
        Additional_facilities::create(['room_id' => '1', 'facilities' => '6']);
        Additional_facilities::create(['room_id' => '1', 'facilities' => '8']);
        Additional_facilities::create(['room_id' => '1', 'facilities' => '4']);

        Season::create([
            'seasonCode' => 'sum_season',
            'seasonName' => 'summer season',
            'start_date' => '2022-01-08',
            'end_date' => '2023-12-28',
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2021-12-09 06:29:54',
            'updated_at' => null
        ]);


        Season::create([
            'seasonCode' => 'win_season',
            'seasonName' => 'winter season',
            'start_date' => '2022-01-23',
            'end_date' => '2023-12-20',
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2021-12-09 06:29:54',
            'updated_at' => null
        ]);


        Agent::create([
            'agentCode' => 'lsr',
            'agentName' => 'lanakan sports rizen',
            'agentEmail' => 'lsr@gmail.com',
            'agentAddress' => '10 aasdsad',
            'agentRating' => '4',
            'agentContactPerson' => 'chris',
            'tel_no_1' => '0814655086',
            'tel_no_2' => null,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2021-12-09 06:29:35',
            'updated_at' => null
        ]);


        Agent::create([
            'agentCode' => 'apple',
            'agentName' => 'apple holidays',
            'agentEmail' => 'apple@gmail.com',
            'agentAddress' => '10 aasdsad',
            'agentRating' => '4',
            'agentContactPerson' => 'ruwan',
            'tel_no_1' => '0814655086',
            'tel_no_2' => null,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2021-12-09 06:29:35',
            'updated_at' => null
        ]);


        Guest::create([
            'passport_id' => '12123132131V',
            'guestFname' => 'sam',
            'guestLname' => 'ranjan',
            'guestAddress' => '10 ward place colombo 02',
            'guestEmail' => 'sam@gmail.com',
            'gcountry' => 'Sri Lanka',
            'contactNo' => '081564545',
            'dob' => '2021-12-07',
            'created_by' => 1,
            'created_at' => '2021-12-09 06:29:35',
            'updated_by' => 1,
            'updated_at' => null
        ]);

        Room_type::create([
            'room_type_id' => '1',
            'room_type_Select' => 'dulux ',
            'room_type_descrption' => 'dulux room',
            'room_type_status' => '1',
            'created_by' => 1,
            'created_at' => '2021-12-09 06:29:54',
            'updated_by' => 1,
            'updated_at' => '2021-12-09 06:29:54',
        ]);

        Room_type::create([
            'room_type_id' => '2',
            'room_type_Select' => 'deluxe king ',
            'room_type_descrption' => 'deluxe king room',
            'room_type_status' => '1',
            'created_by' => 1,
            'created_at' => '2021-12-09 06:29:54',
            'updated_by' => 1,
            'updated_at' => '2021-12-09 06:29:54',
        ]);


        //----------------------------------------------un wanted stuff end -------------------------------------------

        Model::unguard();

        // $this->call("OthersTableSeeder");
    }
}
