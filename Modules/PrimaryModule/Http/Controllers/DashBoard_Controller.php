<?php

namespace Modules\PrimaryModule\Http\Controllers;

use Modules\PrimaryModule\Models\GuestRoom;
use Modules\PrimaryModule\Models\Room;
use Modules\PrimaryModule\Models\Roomallocation;
use Modules\PrimaryModule\Models\RoomReservation;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

use Modules\PrimaryModule\Repositories\CalculatorRepository;

class DashBoard_Controller extends Controller
{
    private $CalculatorRepository;

    public function __construct(CalculatorRepository $CalculatorRepository)
    {
        $this->CalculatorRepository = $CalculatorRepository;
    }
 
    public function index(Request $req)
    {
    
        try {

            $today = date('Y-m-d');
            
            // get the reserved rooms on today
            $notvacantrooms = Roomallocation::where('date','=',$today)->select('roomNumber')->pluck('roomNumber');
            
            if(count($notvacantrooms)<=0){
                $notvacantrooms = ['0'];
            }

            // this will get the vacant rooms that are not reseved in this given date.
            $vacantroomcount = Room::join('roomallocations','rooms.room_id','=','roomallocations.roomNumber')
            ->whereNotIn('rooms.room_id',$notvacantrooms)
            ->groupBy('rooms.room_id')
            ->get();


            // this will get the todays forecast to retrieve paxs count
            $data = $this->CalculatorRepository->calculate_daily_forecast($today);
            
            // this get the inhouse guests count, this uses the reservations with status 2 checked in to 
            // get the relevant guests count

            $inhouse_guests = GuestRoom::join('room_reservations','guest_rooms.res_id','=','room_reservations.id')
            ->where('room_reservations.status','=',2)->count();

            // this will fetch the todays arrival count

            $arrival_count = RoomReservation::where('checkinDate','=',$today)->where('status','=','1')->count();

            $params['vacantrooms'] = count($vacantroomcount);

            $params['meal_plans'] = $data['meals_count']; 

            $params['inhouse_guests'] = $inhouse_guests;

            $params['arrivals'] = $arrival_count;
            
            
        } catch (Exception $e) {
          
            $params['meal_plans'] = [];
            $params['vacantrooms'] = "unknown";
            $params['inhouse_guests'] = "unkown";
            $params['arrivals'] = "unkown";

        }

        return view('primarymodule::pages/dashboard' , $params);
       
    }
}
