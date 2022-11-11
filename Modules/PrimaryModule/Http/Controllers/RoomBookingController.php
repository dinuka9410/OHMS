<?php

namespace Modules\PrimaryModule\Http\Controllers;
use Illuminate\Routing\Controller;
use Modules\PrimaryModule\Models\Room;
use Modules\PrimaryModule\Models\Room_Categories;
use Modules\PrimaryModule\Models\Room_type;
use Modules\PrimaryModule\Models\RoomBookingAllocation;
use Modules\PrimaryModule\Models\RoomBookingfacilities;
use Modules\PrimaryModule\Models\RoomBookingReservation;
use Modules\PrimaryModule\Models\Agent;
use Modules\PrimaryModule\Models\RoomReservation;
use Modules\PrimaryModule\Models\Guest;
use Modules\PrimaryModule\Models\MealPlan;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


use DateTime;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToArray;
use Yajra\DataTables\DataTables;
use Modules\PrimaryModule\Models\Season;

use Modules\PrimaryModule\Repositories\CalculatorRepository;

class RoomBookingController extends Controller
{
    private $CalculatorRepository;

    public function __construct(CalculatorRepository $CalculatorRepository)
    {
        $this->CalculatorRepository = $CalculatorRepository;
    }


    public function room_booking_view(Request $req){


        $params['pagenames'] = [
            [
                'displayname'=>'Booking',
                'routename'=>'booking_reservation_view'
            ],

        ];

          return view('primarymodule::pages/booking_reservation_view',$params);

    }

    public function add_update_booking_view(Request $req){

        $params['pagenames'] = [
            [
                'displayname'=>'Booking',
                'routename'=>'booking_reservation_view'
            ],
            [
                'displayname'=>'Add / Edit Booking',
                'routename'=>'booking_reservation_add_edit'
            ],

        ];


        $params['guests'] = Guest::all();
        $params['basis'] = MealPlan::all();
        $params['agents'] = Agent::all();
        $params['seasons'] = season::all();
        $params['category'] = room_categories::all();

        $all_reservations = roomReservation::with(['get_travel_agent','get_meal_plan','get_guest'])->get();
        $params['all_reservations'] = $all_reservations;
        return view('primarymodule::pages/booking_reservation_add_edit',$params);

    }




    public function add_edit_Booking(Request $req){


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
            ];


           // check if there is a reservation id, if so then it's an edit else a new record

           if($req->booking_id!=null){

            try{

                RoomBookingReservation::where([
                    ['booking_reservations_id',$req->booking_id]
                ])->update([
                    'code'=>$req->booking_code,
                    'resDate'=>$req->indate,
                    'booking_checkinDate'=>$req->indate,
                    'booking_checkoutDate'=>$req->outdate,
                    'agent_id'=>$req->agent_id,
                    'guest_id'=>$req->guest_id,
                    'season_id'=>$req->season_id,
                    'remarks'=>$req->remarks,
                    'user_remarks'=>$req->user_remarks,
                    'status'=>0,
                    'created_by'=>$user->id,
                    'updated_by'=>$user->id,
                    'created_at'=>date("Y-m-d h:i:s"),
                    'updated_at'=>null
                ]);
                $reservation=$req->booking_id;

                $roomallocation = [];

                // get the no of days between checkin date and checkout date

                $checkindate = new DateTime($req->indate);

                $checkoutdate = new DateTime($req->outdate);

                $interval = $checkindate->diff($checkoutdate);

                $rooms = json_decode($req->room_meals);

                // write the rooms to the room allocation table with meal plan
                RoomBookingAllocation::where([['res_id',$reservation]])->delete();

                foreach($rooms as $room){
                  // loop through to add each day of the allocation for the room
                  $date = $req->indate;

                  $dates = (int)$interval->d;
                    for ($i=0; $i <$dates; $i++) {

                        // add the each date to the database

                        $row['roomNumber'] = $room->room_id;
                        $row['res_id'] = $reservation;
                        $row['date'] = $date;
                        $row['basis'] = $room->meal_plan;
                        $row['rate'] = $room->rate;
                        $row['status'] = 1;

                        $date = date('Y-m-d', strtotime($date. ' + 1 days'));

                        $roomallocation[] = $row;

                    }

                }

                RoomBookingAllocation::insert($roomallocation);

                $room_facilities = json_decode($req->add_facilities);

                RoomBookingfacilities::where([['reservation_id',$reservation]])->delete();

                if(count($room_facilities)>0){

                    $records = [];

                    //dd($room_facilities);
                    foreach($room_facilities as $row){

                        foreach($row->facilities as $facility){

                            $final['reservation_id'] = $reservation;
                            $final['room_id'] = $row->room_id;
                            $final['facility_id'] = $facility->add_additional_facilites_id;
                            $final['created_by'] = $user->id;
                            $final['updated_by'] = $user->id;
                            $final['created_at'] = date("Y-m-d h:i:s");

                            $records[] = $final;
                        }

                    }


                    RoomBookingfacilities::insert($records);

                }

                DB::commit();

                $data = [
                    'status'=>'200',
                    'error_status'=>'0',
                    'msg'=>'Room booking edit successful'
                ];

                return redirect()->route('booking_reservation_view')->with('status',$data);

            }catch(QueryException $q){
                DB::rollBack();

                $data = [
                    'status'=>'400',
                    'error_status'=>'2',
                    'msg'=>'Room booking edit failed'
                ];

                return redirect()->route('booking_reservation_add_edit')->with('status',$data);

            }catch(Exception $e){
                DB::rollBack();



                $data = [
                    'status'=>'400',
                    'error_status'=>'2',
                    'msg'=>'Room booking edit failed'
                ];

                return redirect()->route('booking_reservation_add_edit')->with('status',$data);

            }


           }else{

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
                        'contactNo'=>$req->g_contact,
                        'dob'=>$req->g_dob,
                        'booking_checkinDate'=>$user->id,
                        'booking_checkoutDate'=>$user->id,
                        'created_at'=>date("Y-m-d h:i:s"),
                        'updated_at'=>null
                    ]);


                    $req->guest_id = $guest->id;

                }catch(Exception $e){

                   // dd($e);

                    DB::rollBack();

                     $data = [
                         'status'=>'400',
                         'error_status'=>'2',
                         'msg'=>'New guest add failed',
                     ];

                     return redirect()->route('booking_reservation_add_edit')->with('status',$data);

                }

            }else{

                $validation = Validator::make($req->all(),$rules,$msg)->validate();

            }

                try{

                    $new_id = $this->CalculatorRepository->generateID('RB','room_booking','code',$req->indate);
                   // dd($new_id);
                    $reservation = RoomBookingReservation::insertGetId([
                        'code'=>$new_id,
                        'resDate'=>$req->indate,
                        'booking_checkinDate'=>$req->indate,
                        'booking_checkoutDate'=>$req->outdate,
                        'agent_id'=>$req->agent_id,
                        'guest_id'=>$req->guest_id,
                        'season_id'=>$req->season_id,
                        'remarks'=>$req->remarks,
                        'user_remarks'=>$req->user_remarks,
                        'status'=>0,
                        'created_by'=>$user->id,
                        'updated_by'=>$user->id,
                        'created_at'=>date("Y-m-d h:i:s"),
                        'updated_at'=>null
                    ]);

                    $roomallocation = [];

                    // get the no of days between checkin date and checkout date

                    $checkindate = new DateTime($req->indate);

                    $checkoutdate = new DateTime($req->outdate);

                    $interval = $checkindate->diff($checkoutdate);

                    $rooms = json_decode($req->room_meals);

                    // write the rooms to the room allocation table with meal plan
                   $indec=0;
                    foreach($rooms as $room){
                      // loop through to add each day of the allocation for the room
                      $date = $req->indate;
                      $dates = (int) $interval->d;
                        for ($i=0; $i <$dates; $i++) {

                            // add the each date to the database

                            $row['roomNumber'] = $room->room_id;
                            $row['res_id'] = $reservation;
                            $row['date'] = $date;
                            $row['basis'] = $room->meal_plan;
                            $row['rate'] = $room->rate;
                            $row['status'] = 1;
                            $date = date('Y-m-d', strtotime($date. ' + 1 days'));

                            $roomallocation[] = $row;
                            $indec++;
                        }

                    }

                    RoomBookingAllocation::insert($roomallocation);

                    $room_facilities = json_decode($req->add_facilities);

                    if(count($room_facilities)>0){

                        $records = [];

                        //dd($room_facilities);
                        foreach($room_facilities as $row){

                            foreach($row->facilities as $facility){

                                $final['reservation_id'] = $reservation;
                                $final['room_id'] = $row->room_id;
                                $final['facility_id'] = $facility->add_additional_facilites_id;
                                $final['created_by'] = $user->id;
                                $final['updated_by'] = $user->id;
                                $final['created_at'] = date("Y-m-d h:i:s");

                                $records[] = $final;
                            }

                        }


                        RoomBookingfacilities::insert($records);

                    }

                    DB::commit();

                    $data = [
                        'status'=>'200',
                        'error_status'=>'0',
                        'msg'=>'Room booking added successfully'
                    ];

                    return redirect()->route('booking_reservation_view')->with('status',$data);

                }catch(QueryException $q){
                    DB::rollBack();

                    $data = [
                        'status'=>'400',
                        'error_status'=>'2',
                        'msg'=>'Room booking add failed'
                    ];

                    return redirect()->route('booking_reservation_add_edit')->with('status',$data);

                }catch(Exception $e){
                    DB::rollBack();



                    $data = [
                        'status'=>'400',
                        'error_status'=>'2',
                        'msg'=>'Room booking add failed'
                    ];

                    return redirect()->route('booking_reservation_add_edit')->with('status',$data);

                }


                    // end of if reservation id is null
           }

            // end of function
    }
    public function get_vacant_rooms_booking(Request $req){

        // if the request checkin date is small than the todays date then it the request will be rejected
        // cuz reservations cannot be added to pass dates
        $final = [];


        $checkinDate = $req->checkindate;
        $checkoutDate = date('Y-m-d',strtotime($req->checkoutdate.'-1 day'));
        $agent_id = $req->agent_id;
        $season_id = $req->season_id;

        // $checkinDate = '2022-01-02';
        // $checkoutDate = '2022-01-06';
        // $agent_id = 1;


     try{


        $rooms = Room::
        leftjoin('room_booking_allocations',function($join) use($checkinDate,$checkoutDate) {
            $join->on('room_booking_allocations.roomNumber','=','room_id')
            ->join('room_booking', 'room_booking.booking_reservations_id', '=', 'room_booking_allocations.res_id')
            ->whereBetween('room_booking_allocations.date',[$checkinDate,$checkoutDate])
            ->groupBy('room_booking_allocations.roomNumber');
        })
        ->leftjoin('roomallocations',function($join) use($checkinDate,$checkoutDate) {
            $join->on('roomallocations.roomNumber','=','room_id')
            ->join('room_reservations', 'room_reservations.id', '=', 'roomallocations.res_id')
            ->whereBetween('roomallocations.date',[$checkinDate,$checkoutDate])

            ->groupBy('roomallocations.roomNumber');

        })

        ->with(['get_room_type','get_facilities','get_agent_rates'=>function($query)use($agent_id,$season_id){
            $query->where('agent_id','=',$agent_id);
            $query->where('season_id','=',$season_id);
        },'get_agent_rates.get_meal_plan'])
        ->whereNUll('room_booking_allocations.res_id')
        ->whereNUll('roomallocations.res_id')
        ->select('rooms.*')
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

         //dd($rooms);

        return response()->JSON($final);


     }catch(Exception $e){

        // which means failed
        $final['status'] = 0;
        $final['msg'] = 'unable to fetch available rooms';
        return response()->JSON($final);

     }

    }

    // this function will be called from an api route to get all reservations to the data table

    public function get_all_booking(Request $req){

        if($req->type=="search"){

             $checkinDate = $req->checkindate;
             $checkoutDate = $req->checkoutdate;

             $reservations = RoomBookingReservation::where('booking_checkinDate','>=',$checkinDate)
             ->where('booking_checkoutDate','<=',$checkoutDate)
             ->with(['get_travel_agent','get_guest'])->orderBy('code','desc')->get();

             // $reservations = roomReservation::join('roomallocations','room_reservations.id','=','roomallocations.res_id')
             // ->whereBetween('roomallocations.date',[$checkinDate,$checkoutDate])
             // ->with(['get_travel_agent','get_meal_plan','get_guest'])->get();

             return DataTables::of($reservations)
             ->addIndexColumn()
             ->addColumn('action', function($row){
                 if ($row->status == 0) return '<div class="button w-30 bg-theme-12 text-white mt-3"><p>Pending</p></div>';
                 if ($row->status == 1) return '<div class="button w-30 bg-theme-11 text-white mt-3"><p>Confirmed</p></div>';
                 if ($row->status == 2) return '<div class="button w-30 bg-theme-9 text-white mt-3"><p>Checked-in</p></div>';
                 if ($row->status == 3) return '<div class="button w-30 bg-theme-9 text-white mt-2"><p>Check-out</p></div>';
                 if ($row->status == 4) return '<div class="button w-30 bg-theme-9 text-white mt-2"><p>Cancelled</p></div>';
                 if ($row->status == 5) return '<div class="button w-30 bg-theme-6 text-white mt-2"><p>Overwrite</p></div>';
             })
             ->addColumn('info-btn', function($row){
                if ($row->status == 1)
                {
                    $dis_enab="cursor: not-allowed; pointer-events: none; opacity: 0.5;";
                }
                else
                {
                    $dis_enab="";
                }
                if($row->status!=3) return '<div class="flex justify-center items-center mt-2">
                <a style="margin-left: 5%;" href="javascript:;" data-toggle="modal" data-target="#header-footer-modal-preview" class="text-white" onclick="getReservationRooms('.$row->booking_reservations_id.')" ><i class="fa fa-eye" aria-hidden="true"></i></a>
                <a  style="margin-left: 5%;'.$dis_enab.' " href="get_rooms_booking_edit?res_id='.$row->booking_reservations_id.'" class=" text-white" ><i class="fa fa-edit" aria-hidden="true"></i></a>
               <a style="margin-left: 5%;" href="#" onclick="delete_booking('.$row->booking_reservations_id.')" class=" text-white" ><i class="fa fa-trash" aria-hidden="true"></i></a>
                </div>';
                })



              ->rawColumns(['action','info-btn'])

             ->make(true);


        }else{

             $reservations = RoomBookingReservation::with(['get_travel_agent','get_guest'])->orderBy('code','desc')->get();

             return DataTables::of($reservations)
             ->addIndexColumn()
             ->addColumn('action', function($row){
                 if ($row->status == 0) return '<div class="button w-30 bg-theme-12 text-white mt-3"><p>Pending</p></div>';
                 if ($row->status == 1) return '<div class="button w-30 bg-theme-11 text-white mt-3"><p>Confirmed</p></div>';
                 if ($row->status == 2) return '<div class="button w-30 bg-theme-9 text-white mt-3"><p>Checked-in</p></div>';
                 if ($row->status == 3) return '<div class="button w-30 bg-theme-9 text-white mt-2"><p>Check-out</p></div>';
                 if ($row->status == 4) return '<div class="button w-30 bg-theme-9 text-white mt-2"><p>Cancelled</p></div>';
                 if ($row->status == 5) return '<div class="button w-30 bg-theme-6 text-white mt-2"><p>Overwrite</p></div>';
             })
             ->addColumn('info-btn', function($row){
                if ($row->status == 1)
                {
                    $dis_enab="cursor: not-allowed; pointer-events: none; opacity: 0.5;";
                }
                else
                {
                    $dis_enab="";
                }
             if($row->status!=3) return '<div class="flex justify-center items-center mt-2">
             <a style="margin-left: 5%;" href="javascript:;" data-toggle="modal" data-target="#header-footer-modal-preview" class="text-white" onclick="getReservationRooms('.$row->booking_reservations_id.')" ><i class="fa fa-eye" aria-hidden="true"></i></a>
             <a style="margin-left: 5%; '.$dis_enab.'" href="get_rooms_booking_edit?res_id='.$row->booking_reservations_id.'" class=" text-white" ><i class="fa fa-edit" aria-hidden="true"></i></a>
            <a style="margin-left: 5%;" href="#" onclick="delete_booking('.$row->booking_reservations_id.')" class=" text-white" ><i class="fa fa-trash" aria-hidden="true"></i></a>
             </div>';
             })



           ->rawColumns(['action','info-btn'])
            ->make(true);



        }
    }


    public function get_rooms_booking_edit(Request $req){
        //dd($req->res_id);
        $params['pagenames'] = [
            [
                'displayname'=>'Booking',
                'routename'=>'booking_reservation_view'
            ],
            [
                'displayname'=>'Edit Booking',
                'routename'=>'get_rooms_booking_edit'
            ],

        ];
        $reservation_details = RoomBookingReservation::where('booking_reservations_id','=',$req->res_id)->with('get_guest')->first();
        $params['res_details'] = $reservation_details;
        $params['guests'] = Guest::all();
        $params['basis'] = MealPlan::all();
        $params['agents'] = Agent::all();
        $params['bookings'] = RoomBookingReservation::all();
        $params['seasons'] = Season::all();
        $params['category'] = room_categories::all();
        $params['detals']=$req->res_id;
          return view('primarymodule::pages/booking_edit',$params);


    }

 public function get_booked_rooms(Request $req){

        $res_id = $req->res_id;

        //$res_id = 44;

     try{

         $reservation = RoomBookingReservation::where('booking_reservations_id',$res_id)->with(['get_travel_agent','get_guest'])->first();
            // $reservation = roomReservation::where('id',$res_id)->with(['get_travel_agent','get_guest'])->first();

             $rooms = Room::join('room_booking_allocations','rooms.room_id','=','room_booking_allocations.roomNumber')
             ->join('room_booking','room_booking_allocations.res_Id','=','room_booking.booking_reservations_id')
             ->join('meal_plans','room_booking_allocations.basis','meal_plans.id')
             ->where('room_booking.booking_reservations_id',$res_id)
             ->select('rooms.*','meal_plans.mealPlanCode')
             ->distinct('rooms.room_name')
             ->with(['get_room_type','get_category','get_agent_rates'])
             ->get();

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

 public function get_booked_room_facilities(Request $req){

    $res_id = $req->res_id;
    $room_id = $req->room_id;

    $facilities = RoomBookingfacilities::with(['get_facilities'])
    ->where([
       'reservation_id'=>$res_id,
       'room_id'=>$room_id,
    ])->get();

    return response()->JSON($facilities);

}


// this function get the booking details and return as with room allocations
public function get_booking_details(Request $req){

    try{

        $details = RoomBookingReservation::where('booking_reservations_id','=',$req->booking_id)->first();

        $allocation = RoomBookingAllocation::where('res_id','=',$details->booking_reservations_id)
        ->select('room_booking_allocations.roomNumber','room_booking_allocations.basis','room_booking_allocations.rate')
        ->distinct('room_booking_allocations.roomNumber')
        ->get();

        $All_allocation = RoomBookingAllocation::where('res_id','!=',$details->booking_reservations_id)
        ->select('room_booking_allocations.roomNumber','room_booking_allocations.basis','room_booking_allocations.rate')
        ->distinct('room_booking_allocations.roomNumber')
        ->get();

        $final['status'] = 0;

        $final['details'] = $details;
        $final['allocations'] = $allocation;
        $final['All_allocation'] = $All_allocation;

        return response()->JSON($final);


    }catch(Exception $e){

        // which means failed
        $final['status'] = 1;
        $final['msg'] = 'unable to get the details for the particular booking';
        return response()->JSON($final);

    }

}

public function delete_booking(Request $req){

    try {

        $res_id = $req->res_id;

        // this will check whether the reservation to the given ID is not checkin or checkout already
        // which means then it cannot be deleted, but if not checkin or checkout status then delete it

        $reservation = RoomBookingReservation::where('booking_reservations_id','=',$res_id)->first();

        if($reservation->status==2||$reservation->status==3||$reservation->status==1){

            $data = [
                'status'=>500,
                'error_status'=>1,
                'msg'=>'unable to delete this booking, it is already checked in or checkout ',
            ];


        }else{

            DB::beginTransaction();

                RoomBookingfacilities::where('reservation_id','=',$res_id)->delete();

                RoomBookingAllocation::where('res_id','=',$res_id)->delete();

                RoomBookingReservation::where('booking_reservations_id','=',$res_id)->delete();

            DB::commit();

            $data = [
                'status'=>200,
                'error_status'=>0,
                'msg'=>'Booking deleted successfully',
            ];

        }


        return response()->json($data);

    } catch (Exception $e) {

        DB::rollBack();

        $data = [
            'status'=>500,
            'error_status'=>'unable to delete Booking',
            'error_msg'=>$e->getMessage(),
        ];

        return response()->json($data);

    }

}

public function get_vacant_reserved_rooms_booking(Request $req){

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


    $reservation = Room::join('room_booking_allocations','rooms.room_id','=','room_booking_allocations.roomNumber')
    ->where('room_booking_allocations.res_id','=',$reservation_id)
    ->with(['get_room_type','get_facilities','get_agent_rates'=>function($query)use($agent_id,$season_id){

       $query->where('agent_id','=',$agent_id);
       $query->where('season_id','=',$season_id);

   },'get_agent_rates.get_meal_plan','get_room_booking'=>function($query)use($checkinDate,$checkoutDate){

       $query->whereBetween('date',[$checkinDate,$checkoutDate]);

   }])
    ->select('rooms.*','room_booking_allocations.basis','room_booking_allocations.rate')
    ->distinct('rooms.room_name')->get();


    $rooms = Room::leftjoin('room_booking_allocations',function($join) use($checkinDate,$checkoutDate) {
        $join->on('roomNumber','=','room_id')
        ->whereBetween('room_booking_allocations.date',[$checkinDate,$checkoutDate]);
    })
    ->leftjoin('room_booking','room_booking.booking_reservations_id','room_booking_allocations.res_id')
    ->where('rooms.room_status','=','1')
    ->Where(function($q) use($checkinDate,$checkoutDate,$reservation_id){
       $q->whereNUll('room_booking_allocations.res_id');
       $q->orwhere(function($q)use($checkinDate,$checkoutDate,$reservation_id){
           $q->where('room_booking_allocations.date','<',$checkinDate)
           ->where('room_booking_allocations.date','>=',$checkinDate);
       });

       $q->orwhere('room_booking.status','!=',3)
       ->where('room_booking.booking_reservations_id','=',$reservation_id);

    })

    ->with(['get_room_type','get_facilities','get_reservation_facilities_booking'=>function($query)use($reservation_id){

            $query->where('reservation_id','=',$reservation_id);

    },'get_agent_rates'=>function($query)use($agent_id,$season_id){

        $query->where('agent_id','=',$agent_id);
        $query->where('season_id','=',$season_id);

    },'get_agent_rates.get_meal_plan','get_room_booking'=>function($query)use($checkinDate,$checkoutDate){

        $query->whereBetween('date',[$checkinDate,$checkoutDate]);

    }])
    ->select('rooms.*','room_booking.booking_reservations_id as res_id','room_booking_allocations.basis','room_booking_allocations.status')
    ->distinct('rooms.room_name')
    ->orderBy('rooms.room_floor','ASC')
    ->get();

    // end of critical query

    $categories = Room_Categories::get();
    $room_types = Room_type::get();


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

    dd($e);
    // which means failed
    $final['status'] = 0;
    $final['msg'] = 'unable to fetch available rooms';
    return response()->JSON($final);

 }



}


public function get_vacant_rooms(Request $req){


    // if the request checkin date is small than the todays date then it the request will be rejected
    // cuz reservations cannot be added to pass dates

    $final = [];


    $checkinDate = $req->checkindate;
    $checkoutDate = date('Y-m-d',strtotime($req->checkoutdate.'-1 day'));
    $agent_id = $req->agent_id;
    $season_id = $req->season_id;
    $reservation_id = $req->res_id;

 try{



   $reservation = Room::join('room_booking_allocations','rooms.room_id','=','room_booking_allocations.roomNumber')
   ->where('room_booking_allocations.res_id','=',$reservation_id)
   ->with(['get_room_type','get_facilities','get_agent_rates'=>function($query)use($agent_id,$season_id){

      $query->where('agent_id','=',$agent_id);
      $query->where('season_id','=',$season_id);

  },'get_agent_rates.get_meal_plan','get_room_booking'=>function($query)use($checkinDate,$checkoutDate){

      $query->whereBetween('date',[$checkinDate,$checkoutDate]);

  }])
   ->select('rooms.*','room_booking_allocations.basis','room_booking_allocations.rate')
   ->distinct('rooms.room_name')->get();


   $rooms = Room::leftjoin('room_booking_allocations',function($join) use($checkinDate,$checkoutDate) {
       $join->on('roomNumber','=','room_id')
       ->whereBetween('room_booking_allocations.date',[$checkinDate,$checkoutDate]);
   })
   ->leftjoin('room_booking','room_booking.booking_reservations_id','room_booking_allocations.res_id')
   ->where('rooms.room_status','=','1')
   ->Where(function($q) use($checkinDate,$checkoutDate,$reservation_id){
      $q->whereNUll('room_booking_allocations.res_id');
      $q->orwhere(function($q)use($checkinDate,$checkoutDate,$reservation_id){
          $q->where('room_booking_allocations.date','<',$checkinDate)
          ->where('room_booking_allocations.date','>=',$checkinDate);
      });

      $q->orwhere('room_booking.status','!=',0)
      ->where('room_booking.booking_reservations_id','=',$reservation_id);

   })

   ->with(['get_room_type','get_facilities','get_reservation_facilities_booking'=>function($query)use($reservation_id){

           $query->where('reservation_id','=',$reservation_id);

   },'get_agent_rates'=>function($query)use($agent_id,$season_id){

       $query->where('agent_id','=',$agent_id);
       $query->where('season_id','=',$season_id);

   },'get_agent_rates.get_meal_plan','get_room_booking'=>function($query)use($checkinDate,$checkoutDate){

       $query->whereBetween('date',[$checkinDate,$checkoutDate]);

   }])
   ->select('rooms.*','room_booking.booking_reservations_id as res_id','room_booking_allocations.basis','room_booking_allocations.status')
   ->distinct('rooms.room_name')
   ->orderBy('rooms.room_floor','ASC')
   ->get();

   //dd($reservation);
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

    $final['reserved'] = $reservation;
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


public function check_booking_rooms_overriding(Request $req){

    $res_id = $req->id;


    $rooms = json_decode($req->rooms);

    $checkinDate = $req->indate;

    $checkoutDate = date('Y-m-d',strtotime($req->outdate.'-1 day'));

    $allocations = RoomBookingAllocation::join('room_booking','room_booking.booking_reservations_id','=','room_booking_allocations.res_id')
    ->where(function($q)use($checkinDate,$checkoutDate,$res_id){
        $q->whereBetween('date',[$checkinDate,$checkoutDate]);
        // ->where('res_id','=',$res_id);

    })->select('room_booking_allocations.id as allo_id','room_booking_allocations.*','room_booking.code')
    ->distinct('room_booking.code')
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


public function get_booking_rooms(Request $req){

    $res_id = $req->res_id;

    //$res_id = 1;

 try{

     $reservation = RoomBookingReservation::where('booking_reservations_id',$res_id)->with(['get_travel_agent','get_guest'])->first();
        // $reservation = RoomReservation::where('id',$res_id)->with(['get_travel_agent','get_guest'])->first();

         $rooms = Room::join('room_booking_allocations','rooms.room_id','=','room_booking_allocations.roomNumber')
         ->join('room_booking','room_booking_allocations.res_Id','=','room_booking.booking_reservations_id')
         ->join('meal_plans','room_booking_allocations.basis','meal_plans.id')
         ->where('room_booking.booking_reservations_id',$res_id)
         ->select('rooms.*','meal_plans.mealPlanCode','room_booking_allocations.rate')
         ->distinct('rooms.room_name')
         ->with(['get_room_type','get_category','get_agent_rates'])
         ->get();

         $final['rooms'] = $rooms;
         $final['res_details'] = $reservation;
         $final['status'] = 1;

         return response()->JSON($final);

 }catch(Exception $e){
            dd($e);
         // which means failed
         $final['status'] = 0;
         $final['msg'] = 'unable to fetch available rooms';
         return response()->JSON($final);


 }


}

public function booking_delete_by_loop(Request $req){

    $booking_id = $req->res_id;


 try{
    $new_id= RoomBookingReservation::where('booking_reservations_id','=',$booking_id)->get('code');
    RoomBookingReservation::where('booking_reservations_id','=',$booking_id)
                            ->update([
                                'status'=>5,
                                'remarks'=>'This booking is overwritten by booking id '.$new_id[0]->code.' please assign a new room to this booking'
                            ]);
    RoomBookingAllocation::where('res_id','=',$booking_id)->delete();
    RoomBookingfacilities::where('reservation_id','=',$booking_id)->delete();

 }catch(Exception $e){
            dd($e);
         // which means failed
         $final['status'] = 0;
         $final['msg'] = 'unable to delete available rooms';
         return response()->JSON($final);


 }


}


}
