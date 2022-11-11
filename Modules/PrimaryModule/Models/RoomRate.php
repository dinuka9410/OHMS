<?php

namespace Modules\PrimaryModule\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\PrimaryModule\Models\Agent;
use Modules\PrimaryModule\Models\Season;
use Modules\PrimaryModule\Models\Room_type;
use Modules\PrimaryModule\Models\MealPlan;
use Modules\PrimaryModule\Models\Room_Categories;
use App\Models\User;

class RoomRate extends Model
{
    use \Awobaz\Compoships\Compoships;

    public $timestamps = false;
    
    use HasFactory;

    protected $fillable = [
        
        'agent_id','season_id','meal_plan_id','room_type_id','room_category','rate','status','created_by','created_at','updated_by','updated_at'
    
    ];


    public function get_travel_agent(){
        return $this->hasOne(Agent::class,'id','agent_id'); 
    }

    public function get_season(){
        return $this->hasOne(Season::class,'id','season_id');
    }

    public function get_room_type(){
        return $this->hasOne(Room_type::class,'room_type_id','room_type_id');
    }

    public function get_meal_plan(){
        return $this->hasOne(MealPlan::class,'id','meal_plan_id');
    }

    public function get_room_category(){
        return $this->hasOne(Room_Categories::class,'room_categories_id','room_category');
    }
    public function created_user(){
        return $this->hasOne(User::class,'id','created_by');
    }

    public function updated_user(){
        return $this->hasOne(User::class,'id','updated_by');
    }



}
