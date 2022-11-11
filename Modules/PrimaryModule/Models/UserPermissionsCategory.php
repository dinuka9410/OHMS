<?php

namespace Modules\PrimaryModule\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPermissionsCategory extends Model
{
    use HasFactory;
    protected $table = 'user_permissions_category';
    protected $primaryKey = 'id';
    // this function will return the user groups which belongs to eaech permission in the user permission table
    public $timestamps = false;

}
