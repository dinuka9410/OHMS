<?php

namespace Modules\PrimaryModule\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\PrimaryModule\Models\Room_Categories;

class Room_cat_amount extends Model
{

    protected $table = 'room_cat_amounts';
    protected $filltable=['room_cat_amounts_id','room_type_id','room_categories_id','room_cat_amounts_amount'];
    public $timestamps = false;
    protected $primaryKey = 'room_cat_amounts_id';
    use HasFactory;

    public function get_catgory_amount(){
        return $this->hasOne(Room_Categories::class,'room_categories_id','room_categories_id');
    }
   

  
}
