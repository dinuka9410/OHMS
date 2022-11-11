<?php

namespace Modules\PrimaryModule\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cfg_currency extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'c_symbol','c_name','c_rate',
    ];


}
