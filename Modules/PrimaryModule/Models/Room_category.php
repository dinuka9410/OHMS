<?php

namespace Modules\PrimaryModule\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
class Room_category extends Model
{

    protected $table = 'room_categories';
    protected $filltable=['room_categories_id','room_categories_name','area','max_recident','default_recident','max_adults','max_children','status','created_by','created_at','updated_by','updated_at'];
    public $timestamps = false;
    protected $primaryKey = 'room_categories_id';
    use HasFactory;

    public function created_user(){
        return $this->hasOne(User::class,'id','created_by');
    }

    public function updated_user(){
        return $this->hasOne(User::class,'id','updated_by');
    }
}
