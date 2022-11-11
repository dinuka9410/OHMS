<?php

namespace Modules\PrimaryModule\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\PrimaryModule\Models\AddAdditionalFacilites;
class Additional_facilities extends Model
{
    protected $table = 'room_additional_facilities';
    
    protected $filltable=['room_id','facilities'];
    public $timestamps = false;
    protected $primaryKey = 'additional_facilities_id';
    use HasFactory;

    public function get_facilities(){
        
        $result = $this->hasMany(AddAdditionalFacilites::class,'add_additional_facilites_id','facilities');
  
          return $result;
      }
}
