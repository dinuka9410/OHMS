<?php

namespace Modules\PrimaryModule\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\PrimaryModule\Models\Room_type;
use Modules\PrimaryModule\Models\Additional_facilities;
use Modules\PrimaryModule\Models\AddAdditionalFacilites;
use Modules\PrimaryModule\Models\Room_Categories;
use Modules\PrimaryModule\Models\RoomBookingAllocation;
use Modules\PrimaryModule\Models\RoomBookingfacilities;
use App\Models\User;


class Room extends Model
{
    use \Awobaz\Compoships\Compoships;
    
    protected $table = 'rooms';
    protected $filltable=['room_name','room_area','room_Type','room_category','room_max_recident','room_default_recident','room_max_adults','room_max_children','room_beds','room_floor','room_status','room_descrption','room_type_id','Status','Create_date','Update_date','create_by','update_by'];
    public $timestamps = false;
    protected $primaryKey = 'room_id';
    use HasFactory;

    public function created_user(){
        return $this->hasOne(User::class,'id','create_by');
    }

    public function updated_user(){
        return $this->hasOne(User::class,'id','update_by');
    }

    public function get_booking_deatils(){
        return $this->hasOne(RoomBookingAllocation::class,'roomNumber','room_id')->with('get_booking_resevation');
    }

    public function get_room_allocations()
    {

        $data = $this->hasMany(Roomallocation::class, 'roomNumber', 'room_id')->whereHas('room_reservations_unavailable', function ($q) {
            $q->whereNotNull('id');
        })->with('room_reservations_unavailable');

        return $data;
    }

    
    public function get_room_type(){
        return $this->hasOne(Room_type::class,'room_type_id','room_type_id');
    }

    public function get_facilities(){
        
      $result = $this->hasManyThrough(AddAdditionalFacilites::class,Additional_facilities::class,'room_id','add_additional_facilites_id','room_id','facilities');

        return $result;
    }

    public function get_reservation_facilities(){

        $result = $this->hasMany(ReservedRoomFacilities::class,'room_id','room_id');

        return $result;

    }

    public function get_reservation_facilities_booking(){

        $result = $this->hasMany(RoomBookingfacilities::class,'room_id','room_id');

        return $result;

    }

    public function get_category(){
        return $this->hasone(Room_Categories::class,'room_categories_id','room_category');
    }
    public function RoomTypeWithConcat() 
    { 
        return $this->hasOne(Room_type::class,'room_type_id','room_type_id');
    }
    public function RoomCatgoryWithConcat() 
    { 
        return $this->hasOne(Room_Categories::class,'room_categories_id','room_category');
    }


    public function get_agent_rates(){
       $data = $this->hasMany(RoomRate::class,['room_type_id','room_category'],['room_type_id','room_category']);
        return $data;        

    }


    // this will return the meal plan from the room allocation which is the meal plan assgined
    // to that room for tthe specific reservation 

    public function get_reservation_meal_plan(){

        return $this->hasOne(Roomallocation::class,'roomNumber','room_id');

    }


    public function get_room_booking(){

        return $this->hasMany(RoomBookingAllocation::class,'roomNumber','room_id')
               ->join('room_booking','res_id','=','booking_reservations_id');

    }


    


}
