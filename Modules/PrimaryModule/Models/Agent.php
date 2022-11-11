<?php

namespace Modules\PrimaryModule\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Agent extends Model
{
    public $timestamps = false;

    use HasFactory;

    protected $fillable = [
        'agentCode', 'agentName','agentEmail','agentAddress','agentRating','agentContactPerson','tel_no_1','tel_no_2','status','created_by','created_at','updated_by','updated_at'
    ];


    public function created_user(){
        return $this->hasOne(User::class,'id','created_by');
    }

    public function updated_user(){
        return $this->hasOne(User::class,'id','updated_by');
    }


    public function roomRateExists():Attribute{

        return Attribute::get(function(){

            $roomRatesExist = RoomRate::where('agent_id','=',$this->id)->exists();

            return $roomRatesExist;

        });

    }


}
