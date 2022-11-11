<?php

namespace Modules\PrimaryModule\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\PrimaryModule\Models\RoomRate;
use App\Models\User;
class Room_type extends Model
{
    protected $table = 'room_types';

    protected $filltable=['room_type_Select','room_type_area', 'room_type_max_recident','room_type_default_recident','room_type_max_adults','room_type_max_children', 'room_type_descrption','room_type_status','created_by','created_at','updated_by','updated_at'];
    public $timestamps = false;
    protected $primaryKey = 'room_type_id';

    public function get_roomtype_with_rate(){
        return $this->hasOne(RoomRate::class,'room_type_id','room_type_id');
    }
    
    public function created_user(){
        return $this->hasOne(User::class,'id','created_by');
    }

    public function updated_user(){
        return $this->hasOne(User::class,'id','updated_by');
    }

}
