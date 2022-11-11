<?php

namespace Modules\PrimaryModule\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Modules\PrimaryModule\Models\RoomRate;
use Modules\PrimaryModule\Models\RoomReservation;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Season extends Model
{
    public $timestamps = false;
    
    use HasFactory;

    protected $fillable = [
        'seasonCode', 'seasonName','created_by','start_date','end_date','updated_by','status','created_at','updated_at'
    ];


    public function created_user(){
        return $this->hasOne(User::class,'id','created_by');
    }

    public function updated_user(){
        return $this->hasOne(User::class,'id','updated_by');
    }

    public function get_season_with_rate(){
        return $this->hasOne(RoomRate::class,'season_id','id');
    }


    public function bookingsExists():Attribute{

        return Attribute::get(function(){

            $bookingsExists = RoomBookingReservation::where('season_id','=',$this->id)->exists();

            return $bookingsExists;

        });
    }


    public function reservationsExists():Attribute{
        return Attribute::get(function(){
            $reservationsExists = RoomReservation::where('season_id','=',$this->id)->exists();
            return $reservationsExists;
        });
    }

    public function roomRateExists(): Attribute
    {

        return Attribute::get(function(){

            $roomRatesExists = RoomRate::where('season_id','=',$this->id)->exists();
            return $roomRatesExists;

        });
    }


}
