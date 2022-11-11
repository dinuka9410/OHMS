<?php

namespace Modules\PrimaryModule\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class MealPlan extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'mealPlanCode','mealPlanName','created_by','created_at','updated_by','updated_at','status'
    ];


    public function created_user(){
        return $this->hasOne(User::class,'id','created_by');
    }

    public function updated_user(){
        return $this->hasOne(User::class,'id','updated_by');
    }
   

}
