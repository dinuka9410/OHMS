<?php

namespace Modules\PrimaryModule\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Room_category_details extends Model
{

    protected $table = 'room_category_details';
    protected $filltable=['room_category_details_id','room_type_id','room_categories_id','room_type_area','room_type_max_recident','room_type_default_recident','room_type_max_adults','room_type_max_children'];
    public $timestamps = false;
    protected $primaryKey = 'room_category_details_id';
    use HasFactory;

    
}
