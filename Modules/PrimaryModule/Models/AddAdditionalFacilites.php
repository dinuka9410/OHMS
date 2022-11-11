<?php

namespace Modules\PrimaryModule\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddAdditionalFacilites extends Model
{
    protected $table = 'add_additional_facilites';
    
    protected $filltable=['add_additional_facilites_name'];
    public $timestamps = false;
    protected $primaryKey = 'add_additional_facilites_id';
    use HasFactory;
}
