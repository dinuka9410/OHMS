<?php

namespace Modules\PrimaryModule\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\PrimaryModule\Models\UserGroupPermission;

class UserPermission extends Model
{
    use HasFactory;

    // this function will return the user groups which belongs to eaech permission in the user permission table
    public $timestamps = false;

    public function UserGroupPermissions(){

        return $this->hasMany(UserGroupPermission::class,'permission_code','permission_code');

    }
    public function UserPermissionsCategory(){

        return $this->hasOne(UserPermissionsCategory::class,'id','category_id');

    }

}
