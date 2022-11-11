<?php

namespace Modules\PrimaryModule\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room_Categories extends Model
{
    protected $table = 'room_categories';
    
    protected $filltable=['room_categories_id','room_categories_name'];
    public $timestamps = false;
    protected $primaryKey = 'room_categories_id';
    use HasFactory;

    
} 

