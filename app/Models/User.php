<?php

namespace App\Models;

use App\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Modules\PrimaryModule\Models\UserGroup;
use Modules\PrimaryModule\Models\UserPermission;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email','username', 'password','layout','theme','currency_id','user_group_id','photo','address','contact_no',
        'gender','branch_id','status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'photo'=>'string',
    ];

    /**
     * The attributes that appends to returned entities.
     *
     * @var array
     */
    //protected $appends = ['photo'];

    /**
     * The getter that return accessible URL for user photo.
     *
     * @var array
     */
    public function getPhotoAttribute()
    {
        return $this->attributes['photo'];
    }


    public function getUserGroup(){

        return $this->hasOne(UserGroup::class,'user_group_id','user_group_id');

    }
    public function getUserPermission(){

        return $this->hasOne(UserPermission::class,'user_group_id','user_group_id');

    }


}
