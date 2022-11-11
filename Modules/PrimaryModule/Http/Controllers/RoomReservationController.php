<?php

namespace Modules\PrimaryModule\Http\Controllers;

use App\Models\Notification;
use App\Models\Notifications;
use Illuminate\Routing\Controller;
use Modules\PrimaryModule\Models\Agent;
use Illuminate\Http\Request;
use Modules\PrimaryModule\Models\RoomReservation;
use Modules\PrimaryModule\Models\Room;
use Modules\PrimaryModule\Models\Guest;
use Modules\PrimaryModule\Models\MealPlan;
use Modules\PrimaryModule\Models\Roomallocation;
use Modules\PrimaryModule\Models\Room_Categories;
use Modules\PrimaryModule\Models\AddAdditionalFacilites;
use Modules\PrimaryModule\Models\Room_type;
use Modules\PrimaryModule\Models\ReservedRoomFacilities;
use Modules\PrimaryModule\Models\RoomBookingAllocation;
use Modules\PrimaryModule\Models\RoomBookingfacilities;
use Modules\PrimaryModule\Models\guests_list;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use App\Models\User;
use Modules\PrimaryModule\Models\RoomBookingReservation;
use Modules\PrimaryModule\Models\Season;
use Modules\PrimaryModule\Models\GuestRoom;
use Modules\PrimaryModule\Models\Additional_bill;
use Modules\PrimaryModule\Repositories\CalculatorRepository;
use Modules\PrimaryModule\Models\Invoice;
use function App\GetSystemUserCurrency;
use function App\GetSystemUserSymble;
use function App\PermitionChecker;

class RoomReservationController extends Controller
{


    private $CalculatorRepository;

    public function __construct(CalculatorRepository $CalculatorRepository)
    {
        $this->CalculatorRepository = $CalculatorRepository;
    }

    // this method only will return the view, to get the actual reservation data
    // there is api call for the function get all reservations

    public function reservation_view_tab_details(Request $req){

        // this is used in the top page navigation
        // ex : dashboard >> settings >> some other page
        // please provide page name and route name and key value pair in the
        // below associative array

        $params['pagenames'] = [
            [
                'displayname'=>'Reservations',
                'routename'=>'room_reservation_view'
            ],
            [
                'displayname'=>'View',
                'routename'=>'reservation_view_tab_details'
            ],

        ];
       $ispermtion= PermitionChecker(33);

        $Bill = Additional_bill::where('res_id','=',$req->res_id)
        ->with(['getmodule','get_room'])->get();


        $reservations = RoomReservation::where('id','=',$req->res_id)
        ->with(['get_travel_agent','get_guest'])->get();


        if($reservations[0]->booking_id != ""){
        $params['Booking'] = RoomReservation::where('id','=',$req->res_id)
        ->with(['get_travel_agent','get_guest','BK_with_res'])->get();
            }


        $params['ispermtion']=$ispermtion;
        $params['reservations']=$reservations;
          return view('primarymodule::pages/reservation_view_tab_details',$params);

    }

    public function reservation_edit_checkedin(Request $req){

        // this is used in the top page navigation
        // ex : dashboard >> settings >> some other page
        // please provide page name and route name and key value pair in the
        // below associative array

        $params['pagenames'] = [
            [
                'displayname'=>'Room Reservation',
                'routename'=>'room_reservation_view'
            ],
            [
                'displayname'=>'Reservation Checked In Edit',
                'routename'=>'reservation_edit_checkedin'
            ],

        ];

        $reservation_details = RoomReservation::where('id','=',$req->res_id)->with('get_guest')->first();
        $params['guests'] = Guest::all();
        $params['basis'] = MealPlan::all();
        $params['agents'] = Agent::all();
        $params['bookings'] = RoomBookingReservation::all();
        $params['seasons'] = Season::all();
        $params['res_details'] = $reservation_details;
        $params['category'] = room_categories::all();

          return view('primarymodule::pages/reservation_edit_checkedin',$params);

    }




    public function room_reservation_view(Request $req){

        // this is used in the top page navigation
        // ex : dashboard >> settings >> some other page
        // please provide page name and route name and key value pair in the
        // below associative array

        $params['pagenames'] = [
            [
                'displayname'=>'Room Reservation',
                'routename'=>'room_reservation_view'
            ],

        ];
          return view('primarymodule::pages/reservation_view',$params);

    }

// this function will be called from an api route to get all reservations to the data table

    public function get_all_reservations(Request $req){

       if($req->type=="search"){

            $checkinDate = $req->checkindate;
            $checkoutDate = $req->checkoutdate;


            $reservations = RoomReservation::where('checkinDate','>=',$checkinDate)
            ->where('checkoutDate','<=',$checkoutDate)
            ->with(['get_travel_agent','get_guest'])->get();

            // $reservations = roomReservation::join('roomallocations','room_reservations.id','=','roomallocations.res_id')
            // ->whereBetween('roomallocations.date',[$checkinDate,$checkoutDate])
            // ->with(['get_travel_agent','get_meal_plan','get_guest'])->get();

            return DataTables::of($reservations)
            ->addIndexColumn()
            ->addColumn('action', function($row){
                if ($row->status == 1) return '<div class="button w-30 bg-theme-11 text-white mt-3"><p>Confirmed</p></div>';
                if ($row->status == 2) return '<div class="button w-20 bg-theme-9 text-white mt-3"><p>Checked-IN</p></div>';
                if ($row->status == 3) return '<div class="button w-30 bg-theme-13 text-white mt-3" ><p>Check-Out</p></div>';
                if ($row->status == 4) return '<div class="button w-20 bg-theme-9 text-white mt-2"><p>Cancelled</p></div>';
                if ($row->status == 5) return '<div class="button w-30 bg-theme-9 text-white mt-2"><p>Overwritten</p></div>';
            })

            ->addColumn('info-btn', function($row){
                if ($row->status == 2)
                {
                    $usrll='href="reservation_edit_checkedin?res_id='.$row->id.'"';
                }
                else
                {
                    $usrll='href="edit_room_reservation_view?res_id='.$row->id.'"';
                }
                return '<div class="flex justify-center items-center mt-2">
                <a style="margin-left: 5%;"  class=" text-white" href="reservation_view_tab_details?res_id='.$row->id.'"  ><i class="fa fa-eye" aria-hidden="true"></i></a>
                <a style="margin-left: 5%;" '. $usrll.' class=" text-white" ><i class="fa fa-edit" aria-hidden="true"></i></a>
                <a style="margin-left: 5%;" href="#" onclick="deleteReservation('.$row->id.')" class=" text-white" ><i class="fa fa-trash" aria-hidden="true"></i></a></div>';
               })



           ->rawColumns(['action','info-btn'])
            ->make(true);

       }else{

            $reservations = RoomReservation::with(['get_travel_agent','get_guest'])->orderBy('created_at','DESC')->get();

            return DataTables::of($reservations)
            ->addIndexColumn()
            ->addColumn('action', function($row){
                if ($row->status == 1) return '<div class="button w-30 bg-theme-11 text-white mt-3"><p>Confirmed</p></div>';
                if ($row->status == 2) return '<div class="button w-30 bg-theme-9 text-white mt-3"><p>Checked-IN</p></div>';
                if ($row->status == 3) return '<div class="button w-30 bg-theme-13 text-white mt-3" ><p>Check-Out</p></div>';
                if ($row->status == 4) return '<div class="button w-30 bg-theme-9 text-white mt-2"><p>Cancelled</p></div>';
                if ($row->status == 5) return '<div class="button w-30 bg-theme-6 text-white mt-2"><p>Overwritten</p></div>';
            })
            ->addColumn('info-btn', function($row){
                if ($row->status == 2)
                {
                    $usrll='href="reservation_edit_checkedin?res_id='.$row->id.'"';
                }
                else
                {
                    $usrll='href="edit_room_reservation_view?res_id='.$row->id.'"';
                }
             return '<div class="flex justify-center items-center mt-2">
             <a style="margin-left: 5%;" class=" text-white" href="reservation_view_tab_details?res_id='.$row->id.'" ><i class="fa fa-eye" aria-hidden="true"></i></a>
             <a style="margin-left: 5%;" '.$usrll.' class=" text-white" ><i class="fa fa-edit" aria-hidden="true"></i></a>
             <a style="margin-left: 5%;" href="#" onclick="deleteReservation('.$row->id.')" class=" text-white" ><i class="fa fa-trash" aria-hidden="true"></i></a></div>';
            })



            ->rawColumns(['action','info-btn'])
            ->make(true);


       }


    }


    public function get_room_details(Request $req){

        $room_id = $req->room_id;
        $agent_id = $req->agent_id;

        // $room_id = 5;
        // $agent_id = 1;

        $room_details = Room::with(['get_room_type','get_category','get_facilities','get_agent_rates.get_meal_plan'])->where('room_id',$room_id)->first();

        // this will list all the additional facilities to choose from check boxes
        $room_details['all_facilities'] = AddAdditionalFacilites::all();

        //dd($room_details);

        return response()->JSON($room_details);

    }


    public function add_update_reservation_view(Request $req){

        $params['pagenames'] = [
            [
                'displayname'=>'Reservations',
                'routename'=>'room_reservation_view'
            ],
            [
                'displayname'=>'Add / Edit Reservation',
                'routename'=>'add_update_reservation_view'
            ],

        ];


        $params['guests'] = Guest::all();
        $params['basis'] = MealPlan::where('status','=',1)->get();
        $params['agents'] = Agent::where('status','=',1)->get();
        $params['bookings'] = RoomBookingReservation::whereNotIn('status',[1,5])->get();
        $params['seasons'] = Season::where('status','=',1)->get();
        $params['category'] = room_categories::all();
        $all_reservations = RoomReservation::with(['get_travel_agent','get_meal_plan','get_guest'])->get();
        $params['all_reservations'] = $all_reservations;
        return view('primarymodule::pages/reservation_add_edit',$params);

    }


    public function edit_room_reservation_view(Request $req){

        if($req->res_id==null||$req->res_id==""){

            $data = [
                'status'=>'404',
                'error_status'=>'1',
                'msg'=>'invalid reservation id'
            ];
            return redirect()->route('dashboard')->with('status',$data);

        }

        $reservation_details = RoomReservation::where('id','=',$req->res_id)->with('get_guest')->first();

        if($reservation_details==null||$reservation_details==""){

            $data = [
                'status'=>'404',
                'error_status'=>'1',
                'msg'=>'invalid reservation id'
            ];
            return redirect()->route('room_reservation_view')->with('status',$data);

        }


        if($reservation_details->status==3||$reservation_details->status==4){

            $data = [
                'status'=>'404',
                'error_status'=>'1',
                'msg'=>'unable to edit the reservation'
            ];

            return redirect()->route('room_reservation_view')->with('status',$data);

        }

       // dd($reservation_details);

        $params['pagenames'] = [
            [
                'displayname'=>'Reservations',
                'routename'=>'room_reservation_view'
            ],
            [
                'displayname'=>'Edit Reservation',
                'routename'=>'edit_room_reservation_view?res_id='.$req->res_id.''
            ],

        ];

        $params['guests'] = Guest::all();
        $params['basis'] = MealPlan::all();
        $params['agents'] = Agent::all();
        $params['bookings'] = RoomBookingReservation::all();
        $params['seasons'] = Season::all();
        $params['category'] = room_categories::all();
        $params['res_details'] = $reservation_details;

        //dd($req->res_details);

        return view('primarymodule::pages/reservation_edit',$params);

    }


    // get the seasons according to the user selected checkin checkoutdates and agent
    // this function is used to set the seasons for the select drop of seasons in the reservation add page
    // depending on the agent and checkin checkoutdate the seasons shoulds be filtered

    public function get_agent_seasons(Request $req){

        try {

            $checkindate = $req->checkindate;
            $checkoutdate = $req->checkoutdate;
            $agent_id = $req->agent_id;

            $seasons = Season::join('room_rates','seasons.id','=','room_rates.season_id')->where('seasons.start_date','<=',$checkindate)->where('seasons.end_date','>=',$checkoutdate)->where('room_rates.agent_id','=',$agent_id)->groupBy('seasons.id')->get();

            $data = [
                'error_status'=>0,
                'msg'=>'seasons fetched successfully',
                'data'=>$seasons,
            ];


            return response()->json($data);

        } catch (Exception $e) {

            $data = [
                'error_status'=>1,
                'msg'=>'unable to fetch the seasons',
                'error_msg'=>$e->getMessage(),
            ];

            return response()->json($data);

        }

    }


    // this will return the available vacant rooms to the api route get_rooms

    public function get_vacant_rooms(Request $req){

        // if the request checkin date is small than the todays date then it the request will be rejected
        // cuz reservations cannot be added to pass dates

        $final = [];


        $checkinDate = $req->checkindate;
        $checkoutDate = date('Y-m-d',strtotime($req->checkoutdate.'-1 day'));
        $agent_id = $req->agent_id;
        $season_id = $req->season_id;

     try{

        // the most ciritical query of reservation form
        // handle with care.


        $rooms = Room::leftjoin('roomallocations',function($join) use($checkinDate,$checkoutDate) {
            $join->on('roomNumber','=','room_id')
            ->whereBetween('roomallocations.date',[$checkinDate,$checkoutDate]);
        })
        ->leftjoin('room_reservations','room_reservations.id','roomallocations.res_id')
        ->where('rooms.room_status','=','1')
        ->Where(function($q) use($checkinDate,$checkoutDate){
           $q->whereNUll('roomallocations.res_id');
           $q->orwhere(function($q)use($checkinDate,$checkoutDate){
               $q->where('roomallocations.date','<',$checkinDate)
               ->where('roomallocations.date','>=',$checkinDate);
           });
        })

        ->with(['get_room_type','get_facilities','get_agent_rates'=>function($query)use($agent_id,$season_id){

            $query->where('agent_id','=',$agent_id);
            $query->where('season_id','=',$season_id);

        },'get_agent_rates.get_meal_plan','get_room_booking'=>function($query)use($checkinDate,$checkoutDate){

            $query->whereBetween('date',[$checkinDate,$checkoutDate]);

        }])
        ->select('rooms.*')
        ->distinct('rooms.room_name')
        ->orderBy('rooms.room_floor','ASC')
        ->get();

        // end of critical query

        $categories = Room_Categories::get();
        $room_types = Room_type::get();


        $final['categories'] = $categories;
        $final['rooms'] = $rooms;
        $final['types'] = $room_types;

          // which means success
        $final['status'] = 1;

        //dd($final['rooms']);

        return response()->JSON($final);


     }catch(Exception $e){

        //dd($e);

        // which means failed
        $final['status'] = 0;
        $final['msg'] = 'unable to fetch available rooms';
        return response()->JSON($final);

     }


    }


    // this will return the th vacant rooms for the given checkin checkout date along with the
    // particular reservation rooms, this function is used in reservation edit with a reservation id
    // as  parameter

    public function edit_room_reservation_checkin(Request $req){

        $final = [];

        $checkinDate = $req->checkindate;
        $checkoutDate = date('Y-m-d',strtotime($req->checkoutdate.'-1 day'));
        $agent_id = $req->agent_id;

        $reservation_id = $req->res_id;

        //  $checkinDate = '2022-02-13';
        //  $checkoutDate = '2022-02-16';
        //  $agent_id = 1;
        //  $reservation_id = 2;

     try{


         $season = DB::select('SELECT * FROM `seasons` WHERE :test BETWEEN `start_date` and end_date',['test'=>$checkinDate]);

         if(count($season)>0){

            $season_id = $season[0]->id;

        }else{
            $season_id = 0;
        }


        $reservation = Room::join('roomallocations','rooms.room_id','=','roomallocations.roomNumber')
        ->where('roomallocations.res_id','=',$reservation_id)
        ->with(['get_room_type','get_facilities','get_agent_rates'=>function($query)use($agent_id,$season_id){

           $query->where('agent_id','=',$agent_id);
           $query->where('season_id','=',$season_id);

       },'get_agent_rates.get_meal_plan','get_room_booking'=>function($query)use($checkinDate,$checkoutDate){

           $query->whereBetween('date',[$checkinDate,$checkoutDate]);

       }])
        ->select('rooms.*','roomallocations.basis','roomallocations.rate')
        ->distinct('rooms.room_name')->get();


        $rooms = Room::leftjoin('roomallocations',function($join) use($checkinDate,$checkoutDate) {
            $join->on('roomNumber','=','room_id')
            ->whereBetween('roomallocations.date',[$checkinDate,$checkoutDate]);
        })
        ->leftjoin('room_reservations','room_reservations.id','roomallocations.res_id')
        ->where('rooms.room_status','=','1')
        ->Where(function($q) use($checkinDate,$checkoutDate,$reservation_id){
           $q->whereNUll('roomallocations.res_id');
           $q->orwhere(function($q)use($checkinDate,$checkoutDate,$reservation_id){
               $q->where('roomallocations.date','<',$checkinDate)
               ->where('roomallocations.date','>=',$checkinDate);
           });

           $q->orwhere('room_reservations.status','!=',3);

        })

        ->with(['get_room_type','get_facilities','get_reservation_facilities'=>function($query)use($reservation_id){

                $query->where('reservation_id','=',$reservation_id);

        },'get_agent_rates'=>function($query)use($agent_id,$season_id){

            $query->where('agent_id','=',$agent_id);
            $query->where('season_id','=',$season_id);

        },'get_agent_rates.get_meal_plan','get_room_booking'=>function($query)use($checkinDate,$checkoutDate){

            $query->whereBetween('date',[$checkinDate,$checkoutDate]);

        }])
        ->select('rooms.*','room_reservations.id as res_id','roomallocations.basis','roomallocations.status')
        ->distinct('rooms.room_name')
        ->orderBy('rooms.room_floor','ASC')
        ->get();



        $oder_reservation_room = Room::leftjoin('roomallocations',function($join) use($checkinDate,$checkoutDate) {
            $join->on('roomNumber','=','room_id')
            ->whereBetween('roomallocations.date',[$checkinDate,$checkoutDate]);
        })
        ->leftjoin('room_reservations','room_reservations.id','roomallocations.res_id')
        ->where('rooms.room_status','=','1')
        ->Where(function($q) use($checkinDate,$checkoutDate,$reservation_id){
 
           $q->orwhere(function($q)use($checkinDate,$checkoutDate,$reservation_id){
               $q->where('roomallocations.date','<',$checkinDate)
               ->where('roomallocations.date','>=',$checkinDate);
           });

           $q->orwhere('room_reservations.status','!=',3)
           ->where('room_reservations.status','!=',2);

        })

        ->with(['get_room_type','get_facilities','get_reservation_facilities'=>function($query)use($reservation_id){


        },'get_agent_rates'=>function($query)use($agent_id,$season_id){

            $query->where('agent_id','=',$agent_id);
            $query->where('season_id','=',$season_id);

        },'get_agent_rates.get_meal_plan','get_room_booking'=>function($query)use($checkinDate,$checkoutDate){

            $query->whereBetween('date',[$checkinDate,$checkoutDate]);

        }])
        ->select('rooms.*','room_reservations.id as res_id','roomallocations.basis','roomallocations.status','room_reservations.code')
        ->distinct('rooms.room_name')
        ->orderBy('rooms.room_floor','ASC')
        ->get();



        $booked_rooms = Room::
        leftjoin('room_booking_allocations',function($join) use($checkinDate,$checkoutDate) {
            $join->on('room_booking_allocations.roomNumber','=','room_id')
            ->join('room_booking', 'room_booking.booking_reservations_id', '=', 'room_booking_allocations.res_id')
            ->whereBetween('room_booking_allocations.date',[$checkinDate,$checkoutDate])
            ->groupBy('room_booking_allocations.roomNumber');
        })
        ->whereNotNull('room_booking_allocations.res_id')
        ->select('*','room_booking.code')
        ->groupBy('rooms.room_id')
        ->get();
        // end of critical query



        $categories = Room_Categories::get();
        $room_types = Room_type::get();

        $final['booked_rooms'] = $booked_rooms;
        $final['confrom_rooms'] = $oder_reservation_room;
        $final['categories'] = $categories;
        $final['rooms'] = $rooms;
        $final['reserved'] = $reservation;
        $final['types'] = $room_types;
        $final['res'] = $reservation_id;
        $final['checkin'] = $checkinDate;
        $final['checkout'] = $req->checkoutdate;

          // which means success
        $final['status'] = 1;

       // dd($final);

        return response()->JSON($final);


     }catch(Exception $e){

        // which means failed
        $final['status'] = 0;
        $final['msg'] = 'unable to fetch available rooms';
        return response()->JSON($final);

     }



    }



    public function get_vacant_reserved_rooms(Request $req){

        $final = [];

        $checkinDate = $req->checkindate;
        $checkoutDate = date('Y-m-d',strtotime($req->checkoutdate.'-1 day'));
        $agent_id = $req->agent_id;

        $reservation_id = $req->res_id;

        //  $checkinDate = '2022-02-13';
        //  $checkoutDate = '2022-02-16';
        //  $agent_id = 1;
        //  $reservation_id = 2;

     try{


         $season = DB::select('SELECT * FROM `seasons` WHERE :test BETWEEN `start_date` and end_date',['test'=>$checkinDate]);

         if(count($season)>0){

            $season_id = $season[0]->id;

        }else{
            $season_id = 0;
        }


        $reservation = Room::join('roomallocations','rooms.room_id','=','roomallocations.roomNumber')
        ->where('roomallocations.res_id','=',$reservation_id)
        ->with(['get_room_type','get_facilities','get_agent_rates'=>function($query)use($agent_id,$season_id){

           $query->where('agent_id','=',$agent_id);
           $query->where('season_id','=',$season_id);

       },'get_agent_rates.get_meal_plan','get_room_booking'=>function($query)use($checkinDate,$checkoutDate){

           $query->whereBetween('date',[$checkinDate,$checkoutDate]);

       }])
        ->select('rooms.*','roomallocations.basis','roomallocations.rate')
        ->distinct('rooms.room_name')->get();


        $rooms = Room::leftjoin('roomallocations',function($join) use($checkinDate,$checkoutDate) {
            $join->on('roomNumber','=','room_id')
            ->whereBetween('roomallocations.date',[$checkinDate,$checkoutDate]);
        })
        ->leftjoin('room_reservations','room_reservations.id','roomallocations.res_id')
        ->where('rooms.room_status','=','1')
        ->Where(function($q) use($checkinDate,$checkoutDate,$reservation_id){
           $q->whereNUll('roomallocations.res_id');
           $q->orwhere(function($q)use($checkinDate,$checkoutDate,$reservation_id){
               $q->where('roomallocations.date','<',$checkinDate)
               ->where('roomallocations.date','>=',$checkinDate);
           });

           $q->orwhere('room_reservations.status','!=',3)
           ->where('room_reservations.id','=',$reservation_id);

        })

        ->with(['get_room_type','get_facilities','get_reservation_facilities'=>function($query)use($reservation_id){

                $query->where('reservation_id','=',$reservation_id);

        },'get_agent_rates'=>function($query)use($agent_id,$season_id){

            $query->where('agent_id','=',$agent_id);
            $query->where('season_id','=',$season_id);

        },'get_agent_rates.get_meal_plan','get_room_booking'=>function($query)use($checkinDate,$checkoutDate){

            $query->whereBetween('date',[$checkinDate,$checkoutDate]);

        }])
        ->select('rooms.*','room_reservations.id as res_id','roomallocations.basis','roomallocations.status')
        ->distinct('rooms.room_name')
        ->orderBy('rooms.room_floor','ASC')
        ->get();

        $oder_reservation_room = Room::leftjoin('roomallocations',function($join) use($checkinDate,$checkoutDate) {
            $join->on('roomNumber','=','room_id')
            ->whereBetween('roomallocations.date',[$checkinDate,$checkoutDate]);
        })
        ->leftjoin('room_reservations','room_reservations.id','roomallocations.res_id')
        ->where('rooms.room_status','=','1')
        ->Where(function($q) use($checkinDate,$checkoutDate,$reservation_id){
 
           $q->orwhere(function($q)use($checkinDate,$checkoutDate,$reservation_id){
               $q->where('roomallocations.date','<',$checkinDate)
               ->where('roomallocations.date','>=',$checkinDate);
           });

           $q->orwhere('room_reservations.status','!=',3)
           ->where('room_reservations.status','!=',2);

        })

        ->with(['get_room_type','get_facilities','get_reservation_facilities'=>function($query)use($reservation_id){


        },'get_agent_rates'=>function($query)use($agent_id,$season_id){

            $query->where('agent_id','=',$agent_id);
            $query->where('season_id','=',$season_id);

        },'get_agent_rates.get_meal_plan','get_room_booking'=>function($query)use($checkinDate,$checkoutDate){

            $query->whereBetween('date',[$checkinDate,$checkoutDate]);

        }])
        ->select('rooms.*','room_reservations.id as res_id','roomallocations.basis','roomallocations.status')
        ->distinct('rooms.room_name')
        ->orderBy('rooms.room_floor','ASC')
        ->get();
        $booked_rooms = Room::
        leftjoin('room_booking_allocations',function($join) use($checkinDate,$checkoutDate) {
            $join->on('room_booking_allocations.roomNumber','=','room_id')
            ->join('room_booking', 'room_booking.booking_reservations_id', '=', 'room_booking_allocations.res_id')
            ->whereBetween('room_booking_allocations.date',[$checkinDate,$checkoutDate])
            ->groupBy('room_booking_allocations.roomNumber');
        })
        ->whereNotNull('room_booking_allocations.res_id')
        ->select('rooms.*')
        ->groupBy('rooms.room_id')
        ->get();

        // end of critical query

        $categories = Room_Categories::get();
        $room_types = Room_type::get();

        $final['booked_rooms'] = $booked_rooms;
        $final['confrom_rooms'] = $oder_reservation_room;
        $final['categories'] = $categories;
        $final['rooms'] = $rooms;
        $final['reserved'] = $reservation;
        $final['types'] = $room_types;
        $final['res'] = $reservation_id;
        $final['checkin'] = $checkinDate;
        $final['checkout'] = $req->checkoutdate;

          // which means success
        $final['status'] = 1;

       // dd($final);

        return response()->JSON($final);


     }catch(Exception $e){

        // which means failed
        $final['status'] = 0;
        $final['msg'] = 'unable to fetch available rooms';
        return response()->JSON($final);

     }



    }


    // this function add or edit a reservation
    public function add_edit_reservation_checkin(Request $req){



        $user = Auth::user();

            $rules = [
                'guest_id'=>['required'],
                'room_meals'=>['required'],
                'indate'=>['required','date'],
                'outdate'=>['required','date'],
                'agent_id'=>['required','numeric'],

            ];

            $msg = [
                'guest_id.required'=>'please select a guest',
                'indate.required'=>'please select a check-in date',
                'outdate.required'=>'please select a check-out date',
                'agent_id.required'=>'please select a travel agent',
                'room_meals.required'=>'please select at least one vacant room and meal plan',
                'g_email.unique'=>'guest email you provided is already exists',
            ];


           // check if there is a reservation id, if so then it's an edit else a new record

           if($req->reserv_id!=null){


                $res_id = $req->reserv_id;

                // $checkinDate = date('Y-m-d',strtotime($req->indate.'+1 day'));
                $checkinDate = $req->indate;
                $checkoutDate = date('Y-m-d',strtotime($req->outdate.'-1 day'));

                $rooms = json_decode($req->room_meals);


                DB::beginTransaction();

                try{

                    $allocations_booking = RoomBookingAllocation::join('room_booking','room_booking.booking_reservations_id','=','room_booking_allocations.res_id')
                    ->where(function($q)use($checkinDate,$checkoutDate,$res_id){
                        $q->whereBetween('date',[$checkinDate,$checkoutDate]);
                        // ->where('res_id','=',$res_id);
                
                    })->select('room_booking_allocations.id as allo_id','room_booking_allocations.*','room_booking.code')
                    ->distinct('room_booking.code')
                    ->get();

                    $allocations = Roomallocation::join('room_reservations','room_reservations.id','=','roomallocations.res_id')
                    ->where(function($q)use($checkinDate,$checkoutDate,$res_id){
                        $q->whereBetween('date',[$checkinDate,$checkoutDate])
                       ->where('room_reservations.status','!=',3)
                        ->orwhere('res_id','=',$res_id);

                    })->select('roomallocations.id as allo_id','roomallocations.*','room_reservations.*')->get();


                    if(count($allocations)>0 || count($allocations_booking)>0){

                    if(count($allocations)>0){

                        $related_allocations = [];
                        $related_room_id = [];
                        $non_related_res_id = [];
                        $non_related_room_id = [];
                        $non_related_status = [];
                        $non_related_code = [];

                        // if there is allocations first loop through the $allocations array and
                        //get the reservation related allocations to an array,
                        // then get the the non reservation related allocations ands and put them into another array

                        foreach($allocations as $row){

                            if($res_id == $row->res_id){

                                // this reservation related room
                                $related_allocations[] = $row->res_id;
                                $related_room_id[] = $row->roomNumber;

                            }else{

                                // reservation non related but same room number which means overallpping
                                //should be selected and overwrritten with status  5 so that new room should be
                                // assigned to it

                                foreach($rooms as $room){

                                    if($row->roomNumber==$room->room_id){

                                        $non_related_res_id[] = $row->res_id;
                                        $non_related_status[$row->res_id] = $row->status;
                                        $non_related_code[$row->res_id] = $row->code;
                                        $non_related_room_id[] = $row->roomNumber;
                                    }


                                }

                            }

                        }


                        // check whether the user given dates are inbetween a checkin room if so then show
                        // there is a checked in reservation  so unable to update the currenct the given date

                        if(count($non_related_res_id)>0){

                            foreach($non_related_res_id as $res){

                                if($non_related_status[$res]==2||$non_related_status[$res]==3||$non_related_status[$res]==4){

                                    $data = [
                                        'status'=>'400',
                                        'error_status'=>'2',
                                        'msg'=>'unable to edit this reservation because it is blocked by Reservation : '.$non_related_code[$res]
                                    ];

                                    return redirect()->route('room_reservation_view')->with('status',$data);

                                }

                            }

                        }



                        // update the reservation with new checkin checkoutdate agent id

                            if(count($related_allocations)>0){

                                RoomReservation::whereIn('id',$related_allocations)
                                ->update([
                                    'checkinDate'=>$req->indate,
                                    'checkoutDate'=>$req->outdate,
                                    'agent_id'=>$req->agent_id,
                                    'season_id'=>$req->season_id,
                                    'remarks'=>$req->remarks,
                                ]
                                );

                                 // then delete the reservation related allocations
                                 Roomallocation::whereIn('res_id',$related_allocations)->delete();


                            }

                           //dd($non_related_room_id);


                        // next upate the reservation status to 5 (overwritten) which are in the same date range but not
                        // related to reservation

                            if(count($non_related_res_id)>0){


                                roomReservation::whereIn('id',$non_related_res_id)->update(['status'=>5]);
                                Roomallocation::whereIn('roomNumber',$non_related_room_id)->whereIn('res_id',$non_related_res_id)->delete();
                                //Roomallocation::whereIn('roomNumber',$non_related_room_id)->update(['status'=>5]);


                            }

                            ReservedRoomFacilities::where('reservation_id',$req->reserv_id)->delete();
                        }

////////////////////////////////////////////////bookings///////////////////////////////////////////////////////////////


                        if(count($allocations_booking)>0){

                            $booking_related_allocations = [];
                            $booking_related_room_id = [];
                            $booking_non_related_res_id = [];
                            $booking_non_related_room_id = [];
                            $booking_non_related_status = [];
                            $booking_non_related_code = [];
    
                            // if there is allocations first loop through the $allocations array and
                            //get the reservation related allocations to an array,
                            // then get the the non reservation related allocations ands and put them into another array
    
                            foreach($allocations_booking as $row){
    
                                if($res_id == $row->res_id){
    
                                    // this reservation related room
                                    $booking_related_allocations[] = $row->res_id;
                                    $booking_related_room_id[] = $row->roomNumber;
    
                                }else{
    
                                    // reservation non related but same room number which means overallpping
                                    //should be selected and overwrritten with status  5 so that new room should be
                                    // assigned to it
    
                                    foreach($rooms as $room){
    
                                        if($row->roomNumber==$room->room_id){
    
                                            $booking_non_related_res_id[] = $row->res_id;
                                            $booking_non_related_status[$row->res_id] = $row->status;
                                            $booking_non_related_code[$row->res_id] = $row->code;
                                            $booking_non_related_room_id[] = $row->roomNumber;
                                        }
    
    
                                    }
    
                                }
    
                            }

    
    
    
                            // update the reservation with new checkin checkoutdate agent id
    
                                if(count($booking_related_allocations)>0){
    
                                    RoomBookingReservation::whereIn('id',$booking_related_allocations)
                                    ->update([
                                        'checkinDate'=>$req->indate,
                                        'checkoutDate'=>$req->outdate,
                                        'agent_id'=>$req->agent_id,
                                        'season_id'=>$req->season_id,
                                        'remarks'=>$req->remarks,
                                    ]
                                    );
    
                                     // then delete the reservation related allocations
                                     RoomBookingAllocation::whereIn('res_id',$booking_related_allocations)->delete();
    
    
                                }
    
                               //dd($non_related_room_id);
    
    
                            // next upate the reservation status to 5 (overwritten) which are in the same date range but not
                            // related to reservation
    
          
                                if(count($booking_non_related_res_id)>0){
    
    
                                    RoomBookingReservation::whereIn('booking_reservations_id',$booking_non_related_res_id)->update(['status'=>5]);
                                    RoomBookingAllocation::whereIn('roomNumber',$booking_non_related_room_id)->whereIn('res_id',$booking_non_related_res_id)->delete();

    
    
                                }
    
                                RoomBookingfacilities::where('reservation_id',$req->reserv_id)->delete();
                            }

                            // next add the new room allocations

                            $roomallocation = [];

                            // get the no of days between checkin date and checkout date

                             $checkindate = new DateTime($req->indate);

                             $checkoutdate = new DateTime($req->outdate);

                            $interval = $checkindate->diff($checkoutdate);

                              // write the rooms to the room allocation table with meal plan

                            foreach($rooms as $room){

                                $date = $req->indate;

                                for ($i=0; $i <$interval->d; $i++) {

                                    // add the each date to the database

                                    $data['roomNumber'] = $room->room_id;
                                    $data['res_id'] = $res_id;
                                    $data['date'] = $date;
                                    $data['basis'] = $room->meal_plan;
                                    $data['rate'] = $room->rate;
                                    $data['status'] = 1;

                                    $date = date('Y-m-d', strtotime($date. ' + 1 days'));

                                    $roomallocation[] = $data;

                                }

                            }

                            Roomallocation::insert($roomallocation);

                            $room_facilities = json_decode($req->add_facilities);



                            if(count($room_facilities)>0){

                              // dd($room_facilities);

                                $records = [];

                                foreach($room_facilities as $row){

                                    foreach($row->facilities as $facility){

                                        if(isset($facility->add_additional_facilites_id)){

                                            $final['reservation_id'] = $res_id;
                                            $final['room_id'] = $row->room_id;
                                            $final['facility_id'] = $facility->add_additional_facilites_id;
                                            $final['created_by'] = $user->id;
                                            $final['updated_by'] = $user->id;
                                            $final['created_at'] = date("Y-m-d h:i:s");

                                            $records[] = $final;

                                        }

                                    }

                                }


                                ReservedRoomFacilities::where('reservation_id','=',$res_id)->delete();

                                ReservedRoomFacilities::insert($records);

                            }


                            // if the current reservation status is 5 that means the $request incoming is
                            // an edit of overwritten reservation, which means assigning rooms to an
                            // overwritten reservation so if the above process is success then change the
                            // reservation status to 1 (confirmed)

                            if($req->res_status==5){

                                RoomReservation::where('id','=',$res_id)->update(['status'=>1]);

                            }

                           DB::commit();


                           $data = [
                            'status'=>'200',
                            'error_status'=>'0',
                            'msg'=>'room reservation edited successfully! Please update guset alloction in reservation code '.$req->codde
                        ];

                        return redirect()->back()->with('status',$data);

                    }else{

                        // if unable to fetch the allocation for this reservation with or without free rooms


                        $data = [
                            'status'=>'400',
                            'error_status'=>'2',
                            'msg'=>'room reservation edit failed'
                        ];

                        return redirect()->route('room_reservation_view')->with('status',$data);


                    }


                }catch(Exception $e){

                    DB::rollBack();

                    //  --------------- if in anypoint if the reservation edit fails then uncomment the dd and check the error--------------

                  //dd($e);

                    $data = [
                        'status'=>'400',
                        'error_status'=>'2',
                        'msg'=>'room reservation edit failed'
                    ];

                    return redirect()->route('room_reservation_view')->with('status',$data);

                }

           }else{

            // new reservation.................

            DB::beginTransaction();

            // check if there is a new guest and if so then add the guest to the table and get an id
            // for further use

            if(isset($req->f_name)&&isset($req->l_name)){

                $rules += ['g_email'=>['required','email','unique:guests,guestEmail']];
                $validation = Validator::make($req->all(),$rules,$msg)->validate();

                try{

                   $guest =  Guest::create([
                        'passport_id'=>$req->gpass,
                        'guestFname'=>$req->f_name,
                        'guestLname'=>$req->l_name,
                        'guestAddress'=>$req->g_address,
                        'guestEmail'=>$req->g_email,
                        'gcountry'=>$req->g_country,
                        'contactNo'=>$req->g_contact,
                        'dob'=>$req->g_dob,
                        'created_by'=>$user->id,
                        'updated_by'=>$user->id,
                        'created_at'=>date('Y-m-d'),
                        'updated_at'=>null
                    ]);


                    $req->guest_id = $guest->id;

                }catch(Exception $e){

                  //  dd($e);

                    DB::rollBack();

                     $data = [
                         'status'=>'400',
                         'error_status'=>'2',
                         'msg'=>'new guest add failed',
                     ];

                     return redirect()->route('add_update_reservation_view')->with('status',$data);

                }

            }else{

                $validation = Validator::make($req->all(),$rules,$msg)->validate();

            }

                try{

                   // $new_id = Helper::generateID('RES','room_reservations','code',$req->indate);

                   $new_id = $this->CalculatorRepository->generateID('RES','room_reservations','code',$req->indate);

                   if($req->is_a_booking){
                    $bK_id=$req->is_a_booking;
                    }
                    else{
                        $bK_id="";
                    }
                    $reservation = RoomReservation::create([
                        'code'=>$new_id,
                        'resDate'=>$req->indate,
                        'checkinDate'=>$req->indate,
                        'checkoutDate'=>$req->outdate,
                        'agent_id'=>$req->agent_id,
                        'season_id'=>$req->season_id,
                        'guest_id'=>$req->guest_id,
                        'remarks'=>$req->remarks,
                        'status'=>1,
                        'created_by'=>$user->id,
                        'updated_by'=>$user->id,
                        'booking_id'=>$bK_id,
                        'created_at'=>date("Y-m-d h:i:s"),
                        'updated_at'=>null
                        
                    ]);

                    $roomallocation = [];

                    //get the no of days between checkin date and checkout date

                    $checkindate = new DateTime($req->indate);

                    $checkoutdate = new DateTime($req->outdate);

                    $interval = $checkindate->diff($checkoutdate);

                    $rooms = json_decode($req->room_meals);

                    $bookings = [];

                    // write the rooms to the room allocation table with meal plan

                    foreach($rooms as $room){

                        $date = $req->indate;
                        // loop through to add each day of the allocation for the room
                        for ($i=0; $i <$interval->d; $i++) {

                            // add the each date to the database

                            $row['roomNumber'] = $room->room_id;
                            $row['res_id'] = $reservation->id;
                            $row['date'] = $date;
                            $row['basis'] = $room->meal_plan;
                            $row['rate'] = $room->rate;
                            $row['status'] = 1;

                            $date = date('Y-m-d', strtotime($date. ' + 1 days'));

                            $roomallocation[] = $row;
                            $bookings[] = $room->booking;
                        }

                    }


                    Roomallocation::insert($roomallocation);

                    $room_facilities = json_decode($req->add_facilities);

                    if(count($room_facilities)>0){

                        $records = [];

                        foreach($room_facilities as $row){

                            foreach($row->facilities as $facility){

                                $final['reservation_id'] = $reservation->id;
                                $final['room_id'] = $row->room_id;
                                $final['facility_id'] = $facility->add_additional_facilites_id;
                                $final['created_by'] = $user->id;
                                $final['updated_by'] = $user->id;
                                $final['created_at'] = date("Y-m-d h:i:s");

                                $records[] = $final;
                            }

                        }



                        ReservedRoomFacilities::insert($records);

                    }


                    // if there are bookings for the selected rooms then update those booking
                    // to overwritten status (5) and add the remark also

                    // below code only updates the status only is this is not a booking,
                    // cuz if a booking is turn into a reservation then the old booking should be deleted
                    // if the booking is null which means it's not a booking but a reservation and can
                    // have previously booked rooms which can be updated for status 5 and add remark
                    // overwritten by a reservation and cannot use the room plz assign new room


                    if($req->is_a_booking){

                        $booking_id = $req->is_a_booking;

                        //RoomBookingReservation::where('booking_reservations_id','=',$booking_id)->delete();
                        RoomBookingAllocation::where('res_id','=',$booking_id)->delete();
                        RoomBookingfacilities::where('reservation_id','=',$booking_id)->delete();

                        if(count($bookings)>0){

                            foreach($bookings as $row){


                                if($row != $booking_id)
                                {
                                    RoomBookingReservation::where('booking_reservations_id','=',$row)
                                    ->update([
                                        'status'=>5,
                                        'remarks'=>'This booking is overwritten by reservation id '.$new_id.' please assign a new room to this booking'
                                    ]);
                                }

                            }

                        }
                        RoomBookingReservation::where('booking_reservations_id','=',$req->is_a_booking)
                        ->update([
                            'status'=>1,
                            'remarks'=>'This booking is confrmed by reservation id '.$new_id
                        ]);
                    }else{


                    if(count($bookings)>0){

                        foreach($bookings as $row){

                            RoomBookingReservation::where('booking_reservations_id','=',$row)
                            ->update([
                                'status'=>5,
                                'remarks'=>'This booking is overwritten by reservation id '.$new_id.' please assign a new room to this booking'
                            ]);

                        }

                    }


                    }


                    DB::commit();

                     // the confirmation email with room bill sending part

                     $user = Guest::where('id','=',$req->guest_id)->first();

                     $guestemail = $user->guestEmail;

                     $info = $this->CalculatorRepository->get_reservation_total_bill($reservation->id,0);

                     if($info['error_status']==0){

                        $details['to'] = $guestemail;
                        $details['info'] = $info;

                     //  $job = new res_confirm_mail_job($details);

                       // dispatch($job);

                     }


                    // then say successful

                    $data = [
                        'status'=>'200',
                        'error_status'=>'0',
                        'msg'=>'Room reservation added successfully, Reservation Code is '.$new_id
                    ];

                    return redirect()->route('room_reservation_view')->with('status',$data);

                }catch(QueryException $q){



                    DB::rollBack();

                   // dd($q);

                    $data = [
                        'status'=>'400',
                        'error_status'=>'2',
                        'msg'=>'Room reservation add failed'
                    ];

                    return redirect()->route('add_update_reservation_view')->with('status',$data);

                }catch(Exception $e){


                    DB::rollBack();

                 // dd($e);

                    $data = [
                        'status'=>'400',
                        'error_status'=>'2',
                        'msg'=>'Room reservation add failed'
                    ];

                    return redirect()->route('add_update_reservation_view')->with('status',$data);

                }


        // end of if reservation id is null
           }

        // end of function
    }


    public function add_edit_reservation(Request $req){



        $user = Auth::user();

            $rules = [
                'guest_id'=>['required'],
                'room_meals'=>['required'],
                'indate'=>['required','date'],
                'outdate'=>['required','date'],
                'agent_id'=>['required','numeric'],

            ];

            $msg = [
                'guest_id.required'=>'please select a guest',
                'indate.required'=>'please select a check-in date',
                'outdate.required'=>'please select a check-out date',
                'agent_id.required'=>'please select a travel agent',
                'room_meals.required'=>'please select at least one vacant room and meal plan',
                'g_email.unique'=>'guest email you provided is already exists',
            ];


           // check if there is a reservation id, if so then it's an edit else a new record

           if($req->reserv_id!=null){


                $res_id = $req->reserv_id;

                // $checkinDate = date('Y-m-d',strtotime($req->indate.'+1 day'));
                $checkinDate = $req->indate;
                $checkoutDate = date('Y-m-d',strtotime($req->outdate.'-1 day'));

                $rooms = json_decode($req->room_meals);


                DB::beginTransaction();

                try{

                    $allocations = Roomallocation::join('room_reservations','room_reservations.id','=','roomallocations.res_id')
                    ->where(function($q)use($checkinDate,$checkoutDate,$res_id){
                        $q->whereBetween('date',[$checkinDate,$checkoutDate])
                       ->where('room_reservations.status','!=',3)
                        ->orwhere('res_id','=',$res_id);

                    })->select('roomallocations.id as allo_id','roomallocations.*','room_reservations.*')->get();
                    
                    $allocations_booking = RoomBookingAllocation::join('room_booking','room_booking.booking_reservations_id','=','room_booking_allocations.res_id')
                    ->where(function($q)use($checkinDate,$checkoutDate,$res_id){
                        $q->whereBetween('date',[$checkinDate,$checkoutDate]);
                        // ->where('res_id','=',$res_id);
                
                    })->select('room_booking_allocations.id as allo_id','room_booking_allocations.*','room_booking.code')
                    ->distinct('room_booking.code')
                    ->get();


                    if(count($allocations)>0){

                      

                        $related_allocations = [];
                        $related_room_id = [];
                        $non_related_res_id = [];
                        $non_related_room_id = [];
                        $non_related_status = [];
                        $non_related_code = [];

                        // if there is allocations first loop through the $allocations array and
                        //get the reservation related allocations to an array,
                        // then get the the non reservation related allocations ands and put them into another array

                        foreach($allocations as $row){

                            if($res_id == $row->res_id){

                                // this reservation related room
                                $related_allocations[] = $row->res_id;
                                $related_room_id[] = $row->roomNumber;

                            }else{

                                // reservation non related but same room number which means overallpping
                                //should be selected and overwrritten with status  5 so that new room should be
                                // assigned to it

                                foreach($rooms as $room){

                                    if($row->roomNumber==$room->room_id){

                                        $non_related_res_id[] = $row->res_id;
                                        $non_related_status[$row->res_id] = $row->status;
                                        $non_related_code[$row->res_id] = $row->code;
                                        $non_related_room_id[] = $row->roomNumber;
                                    }


                                }

                            }

                        }


                        // check whether the user given dates are inbetween a checkin room if so then show
                        // there is a checked in reservation  so unable to update the currenct the given date

                        if(count($non_related_res_id)>0){

                            foreach($non_related_res_id as $res){

                                if($non_related_status[$res]==2||$non_related_status[$res]==3||$non_related_status[$res]==4){

                                    $data = [
                                        'status'=>'400',
                                        'error_status'=>'2',
                                        'msg'=>'unable to edit this reservation because it is blocked by Reservation : '.$non_related_code[$res]
                                    ];

                                    return redirect()->route('room_reservation_view')->with('status',$data);

                                }

                            }

                        }





                        // update the reservation with new checkin checkoutdate agent id

                            if(count($related_allocations)>0){

                                RoomReservation::whereIn('id',$related_allocations)
                                ->update([
                                    'checkinDate'=>$req->indate,
                                    'checkoutDate'=>$req->outdate,
                                    'agent_id'=>$req->agent_id,
                                    'season_id'=>$req->season_id,
                                    'remarks'=>$req->remarks,
                                ]
                                );

                                 // then delete the reservation related allocations
                                 Roomallocation::whereIn('res_id',$related_allocations)->delete();


                            }

                            if(count($allocations_booking)>0){

                                $booking_related_allocations = [];
                                $booking_related_room_id = [];
                                $booking_non_related_res_id = [];
                                $booking_non_related_room_id = [];
                                $booking_non_related_status = [];
                                $booking_non_related_code = [];
        
                                // if there is allocations first loop through the $allocations array and
                                //get the reservation related allocations to an array,
                                // then get the the non reservation related allocations ands and put them into another array
        
                                foreach($allocations_booking as $row){
        
                                    if($res_id == $row->res_id){
        
                                        // this reservation related room
                                        $booking_related_allocations[] = $row->res_id;
                                        $booking_related_room_id[] = $row->roomNumber;
        
                                    }else{
        
                                        // reservation non related but same room number which means overallpping
                                        //should be selected and overwrritten with status  5 so that new room should be
                                        // assigned to it
        
                                        foreach($rooms as $room){
        
                                            if($row->roomNumber==$room->room_id){
        
                                                $booking_non_related_res_id[] = $row->res_id;
                                                $booking_non_related_status[$row->res_id] = $row->status;
                                                $booking_non_related_code[$row->res_id] = $row->code;
                                                $booking_non_related_room_id[] = $row->roomNumber;
                                            }
        
        
                                        }
        
                                    }
        
                                }
    
        
        
        
                                // update the reservation with new checkin checkoutdate agent id
        
                                    if(count($booking_related_allocations)>0){
        
                                        RoomBookingReservation::whereIn('id',$booking_related_allocations)
                                        ->update([
                                            'checkinDate'=>$req->indate,
                                            'checkoutDate'=>$req->outdate,
                                            'agent_id'=>$req->agent_id,
                                            'season_id'=>$req->season_id,
                                            'remarks'=>$req->remarks,
                                        ]
                                        );
        
                                         // then delete the reservation related allocations
                                         RoomBookingAllocation::whereIn('res_id',$booking_related_allocations)->delete();
        
        
                                    }
        
                                   //dd($non_related_room_id);
        
        
                                // next upate the reservation status to 5 (overwritten) which are in the same date range but not
                                // related to reservation
        
              
                                    if(count($booking_non_related_res_id)>0){
        

                                        RoomBookingReservation::whereIn('booking_reservations_id',$booking_non_related_res_id)->update(['status'=>5,'agent_id'=>$req->agent_id]);
                                        RoomBookingAllocation::whereIn('roomNumber',$booking_non_related_room_id)->whereIn('res_id',$booking_non_related_res_id)->delete();
    
        
        
                                    }
        
                                    RoomBookingfacilities::where('reservation_id',$req->reserv_id)->delete();
                                }
                           //dd($non_related_room_id);


                        // next upate the reservation status to 5 (overwritten) which are in the same date range but not
                        // related to reservation

                            if(count($non_related_res_id)>0){


                                roomReservation::whereIn('id',$non_related_res_id)->update(['status'=>5]);
                                Roomallocation::whereIn('roomNumber',$non_related_room_id)->whereIn('res_id',$non_related_res_id)->update(['status'=>5]);
                                //Roomallocation::whereIn('roomNumber',$non_related_room_id)->update(['status'=>5]);


                            }

                            ReservedRoomFacilities::where('reservation_id',$req->reserv_id)->delete();

                        // next add the new room allocations

                            $roomallocation = [];

                            // get the no of days between checkin date and checkout date

                             $checkindate = new DateTime($req->indate);

                             $checkoutdate = new DateTime($req->outdate);

                            $interval = $checkindate->diff($checkoutdate);

                              // write the rooms to the room allocation table with meal plan

                            foreach($rooms as $room){

                                $date = $req->indate;

                                for ($i=0; $i <$interval->d; $i++) {

                                    // add the each date to the database

                                    $data['roomNumber'] = $room->room_id;
                                    $data['res_id'] = $res_id;
                                    $data['date'] = $date;
                                    $data['basis'] = $room->meal_plan;
                                    $data['rate'] = $room->rate;
                                    $data['status'] = 1;

                                    $date = date('Y-m-d', strtotime($date. ' + 1 days'));

                                    $roomallocation[] = $data;

                                }

                            }

                            Roomallocation::insert($roomallocation);

                            $room_facilities = json_decode($req->add_facilities);



                            if(count($room_facilities)>0){

                              // dd($room_facilities);

                                $records = [];

                                foreach($room_facilities as $row){

                                    foreach($row->facilities as $facility){

                                        if(isset($facility->add_additional_facilites_id)){

                                            $final['reservation_id'] = $res_id;
                                            $final['room_id'] = $row->room_id;
                                            $final['facility_id'] = $facility->add_additional_facilites_id;
                                            $final['created_by'] = $user->id;
                                            $final['updated_by'] = $user->id;
                                            $final['created_at'] = date("Y-m-d h:i:s");

                                            $records[] = $final;

                                        }

                                    }

                                }


                                ReservedRoomFacilities::where('reservation_id','=',$res_id)->delete();

                                ReservedRoomFacilities::insert($records);

                            }


                            // if the current reservation status is 5 that means the $request incoming is
                            // an edit of overwritten reservation, which means assigning rooms to an
                            // overwritten reservation so if the above process is success then change the
                            // reservation status to 1 (confirmed)

                            if($req->res_status==5){

                                RoomReservation::where('id','=',$res_id)->update(['status'=>1]);

                            }

                           DB::commit();


                           $data = [
                            'status'=>'200',
                            'error_status'=>'0',
                            'msg'=>'room reservation edited successfully'
                        ];

                        return redirect()->route('room_reservation_view')->with('status',$data);

                    }else{

                        // if unable to fetch the allocation for this reservation with or without free rooms


                        $data = [
                            'status'=>'400',
                            'error_status'=>'2',
                            'msg'=>'room reservation edit failed'
                        ];

                        return redirect()->route('room_reservation_view')->with('status',$data);


                    }


                }catch(Exception $e){

                    DB::rollBack();

                    //  --------------- if in anypoint if the reservation edit fails then uncomment the dd and check the error--------------

                  //dd($e);

                    $data = [
                        'status'=>'400',
                        'error_status'=>'2',
                        'msg'=>'room reservation edit failed'
                    ];

                    return redirect()->route('room_reservation_view')->with('status',$data);

                }

           }else{

            // new reservation.................

            DB::beginTransaction();

            // check if there is a new guest and if so then add the guest to the table and get an id
            // for further use

            if(isset($req->f_name)&&isset($req->l_name)){

                $rules += ['g_email'=>['required','email','unique:guests,guestEmail']];
                $validation = Validator::make($req->all(),$rules,$msg)->validate();

                try{

                   $guest =  Guest::create([
                        'passport_id'=>$req->gpass,
                        'guestFname'=>$req->f_name,
                        'guestLname'=>$req->l_name,
                        'guestAddress'=>$req->g_address,
                        'guestEmail'=>$req->g_email,
                        'gcountry'=>$req->g_country,
                        'contactNo'=>$req->g_contact,
                        'dob'=>$req->g_dob,
                        'created_by'=>$user->id,
                        'updated_by'=>$user->id,
                        'created_at'=>date('Y-m-d'),
                        'updated_at'=>null
                    ]);


                    $req->guest_id = $guest->id;

                }catch(Exception $e){

                  //  dd($e);

                    DB::rollBack();

                     $data = [
                         'status'=>'400',
                         'error_status'=>'2',
                         'msg'=>'new guest add failed',
                     ];

                     return redirect()->route('add_update_reservation_view')->with('status',$data);

                }

            }else{

                $validation = Validator::make($req->all(),$rules,$msg)->validate();

            }

                try{

                   // $new_id = Helper::generateID('RES','room_reservations','code',$req->indate);

                   $new_id = $this->CalculatorRepository->generateID('RES','room_reservations','code',$req->indate);

                   if($req->is_a_booking){
                    $bK_id=$req->is_a_booking;
                    }
                    else{
                        $bK_id="";
                    }
                    $reservation = RoomReservation::create([
                        'code'=>$new_id,
                        'resDate'=>$req->indate,
                        'checkinDate'=>$req->indate,
                        'checkoutDate'=>$req->outdate,
                        'agent_id'=>$req->agent_id,
                        'season_id'=>$req->season_id,
                        'guest_id'=>$req->guest_id,
                        'remarks'=>$req->remarks,
                        'status'=>1,
                        'created_by'=>$user->id,
                        'updated_by'=>$user->id,
                        'booking_id'=>$bK_id,
                        'created_at'=>date("Y-m-d h:i:s"),
                        'updated_at'=>null
                        
                    ]);

                    $roomallocation = [];

                    //get the no of days between checkin date and checkout date

                    $checkindate = new DateTime($req->indate);

                    $checkoutdate = new DateTime($req->outdate);

                    $interval = $checkindate->diff($checkoutdate);

                    $rooms = json_decode($req->room_meals);

                    $bookings = [];

                    // write the rooms to the room allocation table with meal plan

                    foreach($rooms as $room){

                        $date = $req->indate;
                        // loop through to add each day of the allocation for the room
                        for ($i=0; $i <$interval->d; $i++) {

                            // add the each date to the database

                            $row['roomNumber'] = $room->room_id;
                            $row['res_id'] = $reservation->id;
                            $row['date'] = $date;
                            $row['basis'] = $room->meal_plan;
                            $row['rate'] = $room->rate;
                            $row['status'] = 1;

                            $date = date('Y-m-d', strtotime($date. ' + 1 days'));

                            $roomallocation[] = $row;
                            $bookings[] = $room->booking;
                        }

                    }


                    Roomallocation::insert($roomallocation);

                    $room_facilities = json_decode($req->add_facilities);

                    if(count($room_facilities)>0){

                        $records = [];

                        foreach($room_facilities as $row){

                            foreach($row->facilities as $facility){

                                $final['reservation_id'] = $reservation->id;
                                $final['room_id'] = $row->room_id;
                                $final['facility_id'] = $facility->add_additional_facilites_id;
                                $final['created_by'] = $user->id;
                                $final['updated_by'] = $user->id;
                                $final['created_at'] = date("Y-m-d h:i:s");

                                $records[] = $final;
                            }

                        }



                        ReservedRoomFacilities::insert($records);

                    }


                    // if there are bookings for the selected rooms then update those booking
                    // to overwritten status (5) and add the remark also

                    // below code only updates the status only is this is not a booking,
                    // cuz if a booking is turn into a reservation then the old booking should be deleted
                    // if the booking is null which means it's not a booking but a reservation and can
                    // have previously booked rooms which can be updated for status 5 and add remark
                    // overwritten by a reservation and cannot use the room plz assign new room


                    if($req->is_a_booking){

                        $booking_id = $req->is_a_booking;

                        //RoomBookingReservation::where('booking_reservations_id','=',$booking_id)->delete();
                        RoomBookingAllocation::where('res_id','=',$booking_id)->delete();
                        RoomBookingfacilities::where('reservation_id','=',$booking_id)->delete();

                        if(count($bookings)>0){

                            foreach($bookings as $row){


                                if($row != $booking_id)
                                {
                                    RoomBookingReservation::where('booking_reservations_id','=',$row)
                                    ->update([
                                        'status'=>5,
                                        'remarks'=>'This booking is overwritten by reservation id '.$new_id.' please assign a new room to this booking'
                                    ]);
                                }

                            }

                        }
                        RoomBookingReservation::where('booking_reservations_id','=',$req->is_a_booking)
                        ->update([
                            'agent_id'=>$req->agent_id,
                            'status'=>1,
                            'remarks'=>'This booking is confrmed by reservation id '.$new_id
                        ]);
                    }else{


                    if(count($bookings)>0){

                        foreach($bookings as $row){

                            RoomBookingReservation::where('booking_reservations_id','=',$row)
                            ->update([
                                'status'=>5,
                                'remarks'=>'This booking is overwritten by reservation id '.$new_id.' please assign a new room to this booking'
                            ]);

                        }

                    }


                    }


                    DB::commit();

                     // the confirmation email with room bill sending part

                     $user = Guest::where('id','=',$req->guest_id)->first();

                     $guestemail = $user->guestEmail;

                     $info = $this->CalculatorRepository->get_reservation_total_bill($reservation->id,0);

                     if($info['error_status']==0){

                        $details['to'] = $guestemail;
                        $details['info'] = $info;

                     //  $job = new res_confirm_mail_job($details);

                       // dispatch($job);

                     }


                    // then say successful

                    $data = [
                        'status'=>'200',
                        'error_status'=>'0',
                        'msg'=>'Room reservation added successfully, Reservation Code is '.$new_id
                    ];

                    return redirect()->route('room_reservation_view')->with('status',$data);

                }catch(QueryException $q){



                    DB::rollBack();

                   // dd($q);

                    $data = [
                        'status'=>'400',
                        'error_status'=>'2',
                        'msg'=>'Room reservation add failed'
                    ];

                    return redirect()->route('add_update_reservation_view')->with('status',$data);

                }catch(Exception $e){


                    DB::rollBack();

                 // dd($e);

                    $data = [
                        'status'=>'400',
                        'error_status'=>'2',
                        'msg'=>'Room reservation add failed'
                    ];

                    return redirect()->route('add_update_reservation_view')->with('status',$data);

                }


        // end of if reservation id is null
           }

        // end of function
    }


    // this function will return all the rooms related to the given reservation id along with the room category and type.

    public function get_reservation_rooms(Request $req){

           $res_id = $req->res_id;

           

        try{

            $reservation = RoomReservation::where('id',$res_id)->with(['get_travel_agent','get_guest'])->first();
               // $reservation = RoomReservation::where('id',$res_id)->with(['get_travel_agent','get_guest'])->first();
            $guest_count=GuestRoom::join('guests_lists','guest_rooms.guest_List_id','=','guests_lists.id')->where('guest_rooms.res_id',$res_id)->select('room_no','guesttype', DB::raw('count(*) as total'))->groupBy('guest_rooms.room_no')->groupBy('guests_lists.guesttype')
            ->get();
            $main_guest=GuestRoom::join('guests','guest_rooms.guest_id','=','guests.id')->where('guest_rooms.res_id',$res_id)->select('room_no', DB::raw('count(*) as total'))->groupBy('guest_rooms.room_no')
            ->first();

                $rooms = Room::join('roomallocations','rooms.room_id','=','roomallocations.roomNumber')
                ->join('room_reservations','roomallocations.res_Id','=','room_reservations.id')
                ->join('meal_plans','roomallocations.basis','meal_plans.id')
                ->where('room_reservations.id',$res_id)
                ->select('rooms.*','meal_plans.mealPlanCode','roomallocations.rate')
                ->distinct('rooms.room_name')
                ->with(['get_room_type','get_category','get_agent_rates'])
                ->get();

                $final['main_guest'] = $main_guest;
                $final['guest_count'] = $guest_count;
                $final['rooms'] = $rooms;
                $final['res_details'] = $reservation;
                $final['status'] = 1;

                return response()->JSON($final);

        }catch(Exception $e){

                // which means failed
                $final['status'] = 0;
                $final['msg'] = 'unable to fetch available rooms';
                return response()->JSON($final);


        }


    }


    public function get_reservation_room_facilities(Request $req){

        $res_id = $req->res_id;
        $room_id = $req->room_id;

        $facilities = ReservedRoomFacilities::with(['get_facilities'])
        ->where([
           'reservation_id'=>$res_id,
           'room_id'=>$room_id,
        ])->get();

        return response()->JSON($facilities);

    }


    public function guest_assign_reservation_view(Request $req){

        $params['res_id'] = $req->res_id;

        $params['pagenames'] = [

            [
                'displayname'=>'Reservations',
                'routename'=>'room_reservation_view'
            ],
            [
                'displayname'=>'Reservation check In',
                'routename'=>'guest_assign_reservation_view?res_id='.$req->res_id
            ],

        ];


        $rules = [
            'res_id'=>'required|exists:room_reservations,id',
        ];

        $validation = Validator::make($req->all(),$rules);

        if($validation->fails()){

            return redirect()->route('room_reservation_view');

        }else{

        // RETURN THE THE GUEST LIST FOR SHARING ROOMS
        return view('primarymodule::pages/reservation_share',$params);

        }


    }


        // this function will add the other guests to the reservation so that there is a history of
    // who are the guests in the particular reservation
    // this method also calls the checkin function after adding the additional guests to the reservation


    public function final_checkin(Request $req){


        $rules = [
            'guestfname.*'=>'required',
            'guestlname.*'=>'required',
            'guestpno.*'=>'required',
            'guestroom.*'=>'required',
            'guestdob.*'=>'required',
        ];


        $msgs = [
            'guestfname.*.required'=>'please fill the guest first name fields',
            'guestlname.*.required'=>'please fill the guest last name fields',
            'guestpno.*.required'=>'please fill the guest ID fields',
            'guestroom.*.required'=>'please fill all the guest room fields',
            'guestdob.*.required'=>'please fill all date of births',
        ];

        $data = ['msg'=>'Reservation check-in','url'=>url('primarymodule/reservation_view_tab_details?res_id='.$req->res_id)];
        $notification = new Notifications();
        $notification->createNotification(32,$data);



        try{
            $validation = Validator::make($req->all(),$rules,$msgs)->validate();

            DB::beginTransaction();
            $user = Auth::user();

            $guestcount = count($req->guestfname);

            for ($i=0; $i<$guestcount; $i++) {

                $data['passport_id'] = $req->guestpno[$i];
                $data['guestFname'] = $req->guestfname[$i];
                $data['guestLname'] = $req->guestlname[$i];
                $data['guestAddress'] = $req->guestaddress[$i];
                $data['guestEmail']= $req->gmail[$i];
                $data['gcountry'] = $req->nationality[$i];
                $data['contactNo'] = $req->gcontact[$i];
                $data['dob'] = $req->guestdob[$i];
                $data['created_by'] = $user->id;
                $data['updated_by'] = $user->id;
                $data['created_at'] = Carbon::now();

                $guestreservations = Guest::where([['passport_id','like',$req->guestpno[$i]]])->first();


                if($guestreservations != null || $guestreservations != "" )
                {
                    
                    $id = $guestreservations->id;
                    $data3['res_id'] = $req->res_id;
                    $data3['guest_id'] = $id;
                    $data3['room_no'] = $req->guestroom[$i];
                     GuestRoom::create($data3);
                }
                else
                {
                $data['guesttype'] = $req->guesttype[$i];
                $guests_list = guests_list::updateOrCreate(['passport_id' => $req->guestpno[$i]],$data);

                $data2['res_id'] = $req->res_id;
                $data2['guest_List_id'] = $guests_list->id;

                $data2['room_no'] = $req->guestroom[$i];
                GuestRoom::create($data2);
                }
                

            }


            // //-----------------------------------------------------------

            $reservationid = $req->res_id;

                    $reservation = RoomReservation::where('room_reservations.id','=',$reservationid)
                    ->join('agents','agents.id','=','room_reservations.agent_id')
                    ->first();


                    $today = date("Y-m-d");

                    // this means if the checkin is lesser than today that that means it's a late checkin
                    // so remove the past allocation dates from the table and checkin as today onwards
                    // so the the final bill only will be calculated from today onwards

                    if($today>$reservation->checkinDate){

                        $todaydate = new DateTime($today);
                        $checkindate = new DateTime($reservation->checkinDate);
                        $interval = $todaydate->diff($checkindate);

                        $date = $reservation->checkinDate;

                        $finaldates[] = $date;

                            for($i=1;$i<$interval->d;$i++){

                                $date = date('Y-m-d', strtotime($date. ' + 1 days'));

                                $finaldates[] = $date;

                            }

                        Roomallocation::whereIn('date',$finaldates)->where('res_id','=',$reservationid)->delete();
                        RoomReservation::where('id','=',$reservationid)->update(['checkinDate'=>$today,'status'=>2]);


                    }else{

                        // do the normal checkin for the reservation

                        RoomReservation::where('id','=',$reservationid)->update(['status'=>2]);


                    }


            //--------------------------------------------------------------

        DB::commit();


        $data = [
            'status'=>'200',
            'error_status'=>'0',
            'msg'=>'Reservation checked in successfully'
        ];

        return redirect()->route('room_reservation_view')->with('status',$data);


        }catch(Exception $e){

            DB::rollBack();

            //dd($e);

            $data = [
                'status'=>'400',
                'error_status'=>'2',
                'msg'=>'unable to check-in the reservation'
            ];

            return redirect()->back()->with('status',$data);

        }

    }




    public function check_reservation_rooms_overriding(Request $req){

        $res_id = $req->id;

        $rooms = json_decode($req->rooms);

        $checkinDate = $req->indate;

        $checkoutDate = date('Y-m-d',strtotime($req->outdate.'-1 day'));

        $allocations = Roomallocation::join('room_reservations','room_reservations.id','=','roomallocations.res_id')
        ->where(function($q)use($checkinDate,$checkoutDate,$res_id){
            $q->whereBetween('date',[$checkinDate,$checkoutDate])
           ->where('room_reservations.status','!=',3)
            ->orwhere('res_id','=',$res_id);

        })->select('roomallocations.id as allo_id','roomallocations.*','room_reservations.code')
        ->distinct('room_reservations.code')
        ->get();


        if(count($allocations)>0){

            //dd('meka athule');

            $non_related_res_id = [];
            $non_related_room_id = [];
            $non_related_status = [];
            $non_related_code = [];

            foreach($allocations as $row){

                if($res_id != $row->res_id){


                    foreach($rooms as $room){

                        if($row->roomNumber==$room->room_id){

                            if(!in_array($row->res_id,$non_related_res_id)){

                                $non_related_res_id[] = $row->res_id;
                                $non_related_status[$row->res_id] = $row->status;
                                $non_related_code[] = $row->code;
                                $non_related_room_id[] = $row->roomNumber;

                            }

                        }


                    }


                }

            }

            $data = [
                'status'=>200,
                'error_status'=>0,
                'data'=>$non_related_code,
                'data2'=>$non_related_res_id,
            ];

            return response()->json($data);


        }else{

            $data = [
                'status'=>400,
                'error_status'=>2,
                'msg'=>'something went wrong'
            ];

            return response()->json($data);

        }

     // end of check reservation room overdiing function
    }

    public function check_reservation_rooms_overriding_checkin(Request $req){

        $bookig=[];
        $reser=[];
        $res_id = $req->id;

        $rooms = json_decode($req->rooms);

        $checkinDate = $req->indate;

        $checkoutDate = date('Y-m-d',strtotime($req->outdate.'-1 day'));

        $allocations = Roomallocation::join('room_reservations','room_reservations.id','=','roomallocations.res_id')
        ->where(function($q)use($checkinDate,$checkoutDate,$res_id){
            $q->whereBetween('date',[$checkinDate,$checkoutDate])
           ->where('room_reservations.status','!=',3)
           ->where('room_reservations.status','!=',2);

        })->select('roomallocations.id as allo_id','roomallocations.*','room_reservations.code')
        ->distinct('room_reservations.code')
        ->get();

        $allocations_booking = RoomBookingAllocation::join('room_booking','room_booking.booking_reservations_id','=','room_booking_allocations.res_id')
        ->where(function($q)use($checkinDate,$checkoutDate,$res_id){
            $q->whereBetween('date',[$checkinDate,$checkoutDate]);
            
    
        })->select('room_booking_allocations.id as allo_id','room_booking_allocations.*','room_booking.code')
        ->distinct('room_booking.code')
        ->get();


        if(count($allocations)>0){

 

            $non_related_res_id = [];
            $non_related_room_id = [];
            $non_related_status = [];
            $non_related_code = [];

            foreach($allocations as $row){

                if($res_id != $row->res_id){


                    foreach($rooms as $room){
                        
                        if($row->roomNumber==$room->room_id){
                            //dd($room->room_id);
                            if(!in_array($row->res_id,$non_related_res_id)){

                                $non_related_res_id[] = $row->res_id;
                                $non_related_status[$row->res_id] = $row->status;
                                $non_related_code[] = $row->code;
                                $non_related_room_id[] = $row->roomNumber;
                                $Type[] = 1;
                            }

                        }


                    }


                }

            }

            $reser = [
                'status'=>200,
                'error_status'=>0,
                'data'=>$non_related_code,
                'data2'=>$non_related_res_id,
            ];

    


        }
   
        if(count($allocations_booking)>0){
            $non_related_res_id = [];
            $non_related_room_id = [];
            $non_related_status = [];
            $non_related_code = [];

            foreach($allocations_booking as $row){

                if($res_id != $row->res_id){

                    
                    foreach($rooms as $room){
                       
                        if($row->roomNumber==$room->room_id){
                  
                            if(!in_array($row->res_id,$non_related_res_id)){

                                $non_related_res_id[] = $row->res_id;
                                $non_related_status[$row->res_id] = $row->status;
                                $non_related_code[] = $row->code;
                                $non_related_room_id[] = $row->roomNumber;
                            

                            }

                        }


                    }


                }

            }

            $bookig = [
                'status'=>200,
                'error_status'=>0,
                'data'=>$non_related_code,
                'data2'=>$non_related_res_id,
                
            ];

            

        }
        else{

            $data = [
                'status'=>400,
                'error_status'=>2,
                'msg'=>'something went wrong'
            ];

            

        }

        if(empty($reser))
        {
            $reser="null";
        }
        if(empty($bookig))
        {
            $bookig="null";
        }
        ;
        $data = [
            'status'=>200,
            'error_status'=>0,
            'res'=>$reser,
            'booking'=>$bookig,
        ];

        return response()->json($data);
     // end of check reservation room overdiing function
    }
    


    public function reservation_guestlist(Request $req){

        try{

            $guests = GuestRoom::with(['get_Gusetlist','get_mainGuset'])->where([['res_id',$req->id]])->get();

         $data = [
             'status'=>200,
             'error_status'=>0,
             'data'=>$guests
         ];

         return response()->json($data);

        }catch(Exception $e){

            $data = [
                'status'=>500,
                'error_status'=>1,
                'msg'=>'unable to fetch the guest list'
            ];


            return response()->json($data);

        }

    }


    // this function will return the checkout view for final invoice

    public function checkout_view(Request $req){

        $params['pagenames'] = [

            [
                'displayname'=>'Reservations',
                'routename'=>'room_reservation_view'
            ],

            [
                'displayname'=>'Reservation Checkout',
                'routename'=>'checkout_view?res_id=9'
            ],

        ];


        if($req->res_id==null||$req->res_id==""){

            $data = [
                'status'=>'404',
                'error_status'=>'1',
                'msg'=>'invalid reservation id'
            ];
            return redirect()->route('room_reservation_view')->with('status',$data);

        }

        $params['info'] = $this->CalculatorRepository->get_reservation_total_bill($req->res_id,0);
        $params['currrate'] = GetSystemUserCurrency(1);
        $params['symbol'] = GetSystemUserSymble();
        $params['res_id'] = $req->res_id;

    
        if($params['info']['error_status']!=0){

            $data = [
                'status'=>'404',
                'error_status'=>'1',
                'msg'=>'unable to fetch reservation bills'
            ];
            return redirect()->route('room_reservation_view')->with('status',$data);

        }else{



            $rooms=Roomallocation::where('res_id','=',$req->res_id)->with(['get_meal_plan','get_room'])->groupBy('roomNumber')->get();

            return view('primarymodule::pages/checkout_view',$params);

        }

    }


    public function deleteReservation(Request $req){

        try {

            $res_id = $req->res_id;

            // this will check whether the reservation to the given ID is not checkin or checkout already
            // which means then it cannot be deleted, but if not checkin or checkout status then delete it

            $reservation = RoomReservation::where('id','=',$res_id)->first();

            if($reservation->status==2||$reservation->status==3){

                $data = [
                    'status'=>500,
                    'error_status'=>1,
                    'msg'=>'unable to delete this reservation, it is already checked in or checkout ',
                ];


            }else{

                DB::beginTransaction();

                    $BK_id= RoomReservation::where('id','=',$req->res_id)->with(['get_travel_agent','get_guest','BK_with_res'])->get();
                    if(isset($BK_id[0]->BK_with_res))
                    {
                        RoomBookingReservation::where('booking_reservations_id','=',$BK_id[0]->BK_with_res->booking_reservations_id)->update(['status' => 0,'remarks' => 'This confirmed booking revert by deleting Reservation code '.$BK_id[0]->BK_with_res->code]);
                    }
        
                    ReservedRoomFacilities::where('reservation_id','=',$res_id)->delete();

                    Roomallocation::where('res_id','=',$res_id)->delete();

                    RoomReservation::where('id','=',$res_id)->delete();

                DB::commit();

                $data = [
                    'status'=>200,
                    'error_status'=>0,
                    'msg'=>'reservation deleted successfully',
                ];

            }


            return response()->json($data);

        } catch (Exception $e) {

            DB::rollBack();

            $data = [
                'status'=>500,
                'error_status'=>'unable to delete reservation',
                'error_msg'=>$e->getMessage(),
            ];

            return response()->json($data);

        }

    }

    public function insrt_guest_list(Request $req){
      
        $rules = [
            'fname.*'=>'required',
            'lname.*'=>'required',
            'g_contact.*'=>'required',
            'guest_room.*'=>'required',
            'dob.*'=>'required',
        ];


        $msgs = [
            'fname.*.required'=>'please fill the guest first name fields',
            'lname.*.required'=>'please fill the guest last name fields',
            'g_contact.*.required'=>'please fill the guest ID fields',
            'guest_room.*.required'=>'please fill all the guest room fields',
            'dob.*.required'=>'please fill all date of births',
        ];

        $data = ['msg'=>'New Room CheckIn','url'=>url('primarymodule/room_reservation_view')];
        $notification = new Notifications();
        $notification->createNotification(32,$data);

       

        try{
            $validation = Validator::make($req->all(),$rules,$msgs)->validate();

            DB::beginTransaction();
            $user = Auth::user();


                $data['passport_id'] = $req->gpass;
                $data['guestFname'] = $req->fname;
                $data['guestLname'] = $req->lname;
                $data['guestAddress'] = $req->address;
                $data['guestEmail']= $req->g_email;
                $data['gcountry'] = $req->nationality;
                $data['contactNo'] = $req->g_contact;
                $data['dob'] = $req->dob;
                $data['created_by'] = $user->id;
                $data['updated_by'] = $user->id;
                $data['created_at'] = Carbon::now();

                
                $guestreservations = Guest::where([['passport_id','like',$req->gpass]])->first();

                if($guestreservations != null || $guestreservations != "" )
                {
                    
                    $id = $guestreservations->id;
                    $data3['res_id'] = $req->res_id;
                    $data3['guest_id'] = $id;
                    $data3['room_no'] = $req->guest_room;
                     GuestRoom::create($data3);
                }
                else
                {
                $data['guesttype'] = $req->guest_type;
                $guests_list = guests_list::updateOrCreate(['passport_id' => $req->gpass],$data);

                $data2['res_id'] = $req->res_id;
                $data2['guest_List_id'] = $guests_list->id;
                $data2['room_no'] = $req->guest_room;
                GuestRoom::create($data2);
                }
               

       

        DB::commit();


        $dataM = [
            'status'=>'200',
            'error_status'=>'0',
            'msg'=>'Guest added successfully'
        ];

        return back()->with('status',$dataM);


        }catch(Exception $e){
   
            DB::rollBack();
         
            $data = [
                'status'=>'400',
                'error_status'=>'2',
                'msg'=>'unable to update'
            ];

            return redirect()->back()->with('status',$data);

        }

    }


    public function update_guest_list(Request $req){
      
   
        $rules = [
            'fname.*'=>'required',
            'lname.*'=>'required',
            'g_contact.*'=>'required',
            'guest_room.*'=>'required',
            'dob.*'=>'required',
        ];


        $msgs = [
            'fname.*.required'=>'please fill the guest first name fields',
            'lname.*.required'=>'please fill the guest last name fields',
            'g_contact.*.required'=>'please fill the guest ID fields',
            'guest_room.*.required'=>'please fill all the guest room fields',
            'dob.*.required'=>'please fill all date of births',
        ];

        $data = ['msg'=>'New Room CheckIn','url'=>url('primarymodule/room_reservation_view')];
        $notification = new Notifications();
        $notification->createNotification(32,$data);

       

        try{
            $validation = Validator::make($req->all(),$rules,$msgs)->validate();

            DB::beginTransaction();
            $user = Auth::user();


                

                
                $guestreservations = Guest::where([['passport_id','like',$req->gpass]])->where('id', $req->gu_id)->first();

                if($guestreservations != null || $guestreservations != "" )
                {

                $data0['passport_id'] = $req->gpass;
                $data0['guestFname'] = $req->fname;
                $data0['guestLname'] = $req->lname;
                $data0['guestAddress'] = $req->address;
                $data0['guestEmail']= $req->g_email;
                $data0['gcountry'] = $req->nationality;
                $data0['contactNo'] = $req->g_contact;
                $data0['dob'] = $req->dob;
                $data0['updated_by'] = $user->id;
                $data0['updated_at'] = Carbon::now();
                    
                    Guest::where('id', $req->gu_id)->update($data0);

                    $data3['room_no'] = $req->guest_room;
                     GuestRoom::where('guest_id', $req->gu_id)->where('res_id', $req->res_id)->update($data3);
                }
                else
                {

                    $data0['passport_id'] = $req->gpass;
                    $data0['guestFname'] = $req->fname;
                    $data0['guestLname'] = $req->lname;
                    $data0['guestAddress'] = $req->address;
                    $data0['guestEmail']= $req->g_email;
                    $data0['gcountry'] = $req->nationality;
                    $data0['contactNo'] = $req->g_contact;
                    $data0['dob'] = $req->dob;
                    $data0['updated_by'] = $user->id;
                    $data0['updated_at'] = Carbon::now();

                $data['guesttype'] = $req->guest_type;
                guests_list::where('id', $req->gu_id)->update($data0);

                $data2['room_no'] = $req->guest_room;
                GuestRoom::where('guest_List_id', $req->gu_id)->where('res_id', $req->res_id)->update($data2);

                }
               

       

        DB::commit();


        $dataM = [
            'status'=>'200',
            'error_status'=>'0',
            'msg'=>'Guest update successfully'
        ];

        return back()->with('status',$dataM);


        }catch(Exception $e){

            DB::rollBack();
         
            $data = [
                'status'=>'400',
                'error_status'=>'2',
                'msg'=>'unable to update guest '
            ];

            return redirect()->back()->with('status',$data);

        }

    }

    public function delete_res_guest(Request $req){


        try{


            DB::beginTransaction();

                
                $guestreservations = Guest::where([['passport_id','like',$req->gpass]])->where('id', $req->G_id)->first();

                if($guestreservations != null || $guestreservations != "" )
                {

                GuestRoom::where('guest_id', $req->G_id)->where('res_id', $req->resid)->delete();
                }
                else
                {

                $dd=GuestRoom::where('guest_List_id', $req->G_id)->where('res_id', $req->resid)->delete();

                }
               

       

        DB::commit();


        $dataM = [
            'status'=>'200',
            'error_status'=>'0',
            'msg'=>'Guest delete successfully'
        ];

        return back()->with('status',$dataM);


        }catch(Exception $e){

            DB::rollBack();
         
            $data = [
                'status'=>'400',
                'error_status'=>'2',
                'msg'=>'unable to delete guest '
            ];

            return redirect()->back()->with('status',$data);

        }

    }

    public function get_all_checkOutRoom(Request $req){


        try{
        $data['info'] = $this->CalculatorRepository->get_reservation_total_bill($req->res_id,0);
        $data['data']=Roomallocation::where('res_id','=',$req->res_id)->with(['get_meal_plan','get_room','res_buy_allocation'])->groupBy('roomNumber')->get();
        $data['done']=Invoice::where('res_id','=',$req->res_id)->with(['get_invo_romms'])->get();

        
        
    }catch(Exception $e){

        DB::rollBack();
     
        $data = [
            'status'=>'400',
            'error_status'=>'2',
            'msg'=>'unable to delete guest '
        ];
    }
    return response()->JSON($data);
    }

}
