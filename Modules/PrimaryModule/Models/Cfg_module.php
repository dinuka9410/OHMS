<?php

namespace Modules\PrimaryModule\Models;

use Illuminate\Database\Eloquent\Model;

class Cfg_module extends Model
{
    protected $table = 'cfg_module';

    public $timestamps = false;
    protected $primaryKey = 'id';

    public function ActiveModule(){
        return $this->where('status','1');
    }

    public function FileActive()
    {
        return $this->where('files_status','1');
    }
    public function FileInactive()
    {
        return $this->where('files_status','0');
    }
    public function ModuleExists($module_name)
    {
        return $this->where('module_name',$module_name)->where('status','1')->exists();
    }

    public function relatedPermissions(){

        return $this->hasMany(UserPermission::class,'module_id','id')->with('userpermissionscategory')->orderBY('category_id','asc');

    }
}
