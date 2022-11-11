<?php

namespace App;

use DateTime;
use Illuminate\Support\Facades\Session;
use Modules\PrimaryModule\Models\UserGroupPermission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

    if(!function_exists('GetSystemUserCurrency')){

        function GetSystemUserCurrency($Val){
            $rate=Session::get('currency_rate');
            $newrate=($rate[0]);
            $tempamount=floatval($newrate)*floatval($Val);
            $finalamout = number_format($tempamount, 5);
            return $finalamout;

        }

    }


    if(!function_exists('GetSystemUserCurrency_wv')){

        function GetSystemUserCurrency_wv(){
            $rate=Session::get('currency_rate');
            $newrate=($rate[0]);
            $tempamount=doubleval($newrate)*1;
            $finalamout = number_format($tempamount, 2);
            return $finalamout;

        }
    }


    if(!function_exists('GetSystemUserCurrency_convertion')){

        function GetSystemUserCurrency_convertion($Val){
            $rate=Session::get('currency_rate');
            $newrate=($rate[0]);
            $tempamount=doubleval($Val)/doubleval($newrate);
            $finalamout = number_format($tempamount, 10);
            return $finalamout;

        }

    }


    if(!function_exists("GetSystemUserCurrency_convertion_without_fromat")){

        function GetSystemUserCurrency_convertion_without_fromat($Val){
            $rate=Session::get('currency_rate');
            $newrate=($rate[0]);
            $tempamount=doubleval($Val)/doubleval($newrate);
            return $tempamount;
        }
    }

    if(!function_exists("GetSystemUserSymble")){

        function GetSystemUserSymble(){
            $Symble=Session::get('currency_symbol');
            $newSymble=($Symble[0]);
            return $newSymble;

        }
    }
    if(!function_exists("FormatDateTime")){

        function FormatDateTime($dateTime)
        {
            $date = new DateTime($dateTime);
            return $date->format('Y-m-d H:i');
        }
    }
    if(!function_exists("PermitionChecker")){

        function PermitionChecker($permition_id)
        {
            $user = Auth::user();
            $permission = Cache::remember('permission', 30, function () use ($user) {
            return UserGroupPermission::with('userpermission')->where(['user_group_id' => $user->user_group_id])->get();
        });
        //dd($permission);
        if ($permission->where('permission_code', $permition_id)->count() > 0) 
        {
            return 1;
        }
        else
        {
            return 0; 
        }
        }
    }



