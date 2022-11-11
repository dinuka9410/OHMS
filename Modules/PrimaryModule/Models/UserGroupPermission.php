<?php

namespace Modules\PrimaryModule\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGroupPermission extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = ['user_group_id','permission_code'];

    public function UserPermission(){

        return $this->hasOne(UserPermission::class,'permission_code','permission_code');

    }
}
