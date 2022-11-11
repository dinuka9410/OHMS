<?php

namespace Modules\PrimaryModule\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Modules\PrimaryModule\Models\Guest;
use Modules\PrimaryModule\Models\guests_list;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Modules\PrimaryModule\Models\GuestRoom;
use Modules\PrimaryModule\Models\RoomBookingReservation;
use Modules\PrimaryModule\Models\RoomReservation;
use Yajra\DataTables\DataTables;

class GuestController extends Controller
{
    public function guests_view(Request $req){


        $params['pagenames'] = [
            [
                'displayname'=>'Guests',
                'routename'=>'guests_view'
            ],
   
        ];

        return view('primarymodule::pages/guest_view',$params);

    }
    public function edit_guest_list(Request $req){



        if($req->flg == 1)
        {
            $Guest=GuestRoom::join('guests','guest_rooms.guest_id','=','guests.id')
            ->where([['guest_rooms.res_id',$req->resid]])
            ->where([['guest_rooms.guest_id',$req->G_id]])
            ->where([['guests.id',$req->G_id]])
            ->first();
            return response()->json($Guest);
        }
        if($req->flg == 2)
        {
            $Guest=GuestRoom::join('guests_lists','guest_rooms.guest_List_id','=','guests_lists.id')
            ->where([['guest_rooms.res_id',$req->resid]])
            ->where([['guest_rooms.guest_List_id',$req->G_id]])
            ->where([['guests_lists.id',$req->G_id]])
            ->first();
            return response()->json($Guest);
        }

    }

    
    public function Get_res_Guest(Request $req){


        try {
            

            $Guest = GuestRoom::with(['get_Gusetlist','get_mainGuset'])->where([['res_id',$req->resid]])->get();
            
        
            return response()->json($Guest);
        

        } catch (Exception $e) {
         
            $data = [
                'status'=>'400',
                'error_status'=>'1',
                'msg'=>'guest fetch failed'
            ];
            
            return response()->json($data);
        }
    }


    public function GetGuset_by_nic(Request $req){


        try {
            
            $nic = $req->nic;

            $guestreservations = Guest::where([['passport_id','like',$nic]])->first();

            if($guestreservations == null || $guestreservations == "" )
            {
                $guests_list = guests_list::where([['passport_id','like',$nic]])->first();
                return response()->json($guests_list);
            }
            else
            {
                return response()->json($guestreservations);
            }

            
            

        } catch (Exception $e) {
           
            $data = [
                'status'=>'400',
                'error_status'=>'1',
                'msg'=>'guest fetch failed'
            ];
            
            return response()->json($data);
        }
    }

    // this is used for the data table of guests view

    public function getAllGuests(Request $req){

        $guests = Guest::orderBy('created_at',"DESC")->get();

        return DataTables::of($guests)
        ->addIndexColumn()
        ->addColumn('info-btn', function($row){
            return '<div class="flex justify-center items-center mt-2"><a href="javascript:;" data-toggle="modal" data-target="#header-footer-modal-preview" class="" onclick="getGuestsReservations('.$row->id.')"  ><i class="fa fa-eye" aria-hidden="true"></i></a><a style="margin-top: 0%; margin-left:10%;" class="flex items-center mr-3 text-theme-1" href="add_update_guest_view?id='.$row->id.'"><i class="fas fa-edit"></i></a><a style="margin-top: 0%; margin-left:5%;" class="flex items-center mr-3 text-theme-1" href="#" onclick="deleteGuest('.$row->id.')" ><i class="fas fa-trash"></i></a></div>';
           })

            
        ->rawColumns(['info-btn'])
        ->make(true);
    }


    public function getAllGuestReservations(Request $req){

        try {
            
            $guest_id = $req->guest_id;

            $guestreservations = Guest::join('room_reservations','room_reservations.guest_id','=','guests.id')
            ->join('agents','agents.id','=','room_reservations.agent_id')->where('room_reservations.status','=',$guest_id)->get();

            $data = [
                'status'=>'200',
                'error_status'=>'0',
                'msg'=>'guest reservations fetched successfully',
                'data'=>$guestreservations,
            ];

            return response()->json($data);
            

        } catch (Exception $e) {
           
            $data = [
                'status'=>'400',
                'error_status'=>'1',
                'msg'=>'guest reservations fetch failed'
            ];
            
            return response()->json($data);
        }

    }


    public function add_update_guest_view(Request $req){


        $params['pagenames'] = [
        
            [
                'displayname'=>'Guest View',
                'routename'=>'guests_view'
            ],
    
            [
                'displayname'=>'Add / Edit Guest',
                'routename'=>'add_update_guest_view'
            ],
    
        ];


         // get all the guests to show in the table

        $params['guests'] = Guest::all();


        if(isset($req->id)){

            $details = Guest::with(['created_user','updated_user'])->where([['id',$req->id]])->first();

            if($details){

                // is there is relvant data then append to the status info
                $params['details'] = $details;
                $params['status_info'] = array('created_by' =>$details->created_user->username,'created_at'=>$details->created_at,'updated_by'=>$details->updated_user->username,'updated_at'=>$details->updated_at);


            }

            return view('primarymodule::pages/guest_add_update',$params);

        }
          // if there is no req->id then it's a new entry so return a empty form
        else{

            return view('primarymodule::pages/guest_add_update',$params);

        }

    }



    // this function will actaully add or update the agent to db if valid


    public function guest_add_edit(Request $req){

         // if there is an guest id that means the data should be updated not add a new record

        if(isset($req->guest_id)&&$req->guest_id!=''){

            $rules = [
                'gpass'=>['required'],
                'gfname'=>['required','string'],
                'glname'=>['required','string'],
            ];
    
            $msg = [
                'gpass.required'=>'please enter a valid guest Passport / NIC No',
                'gfname.required'=>'please provide a guest first name',
                'glname.required'=>'please provide a guest lastname',
                'gemail.required'=>'please provide a vaild email',
                'gcontact.required'=>'please provide a guest contact no',
            ];

            $user = Auth::user();

            $validation = Validator::make($req->all(),$rules,$msg)->validate();

            DB::beginTransaction();

            try{

                Guest::where('id',$req->guest_id)->update([
                    'passport_id'=>$req->gpass,
                    'guestFname'=>$req->gfname,
                    'guestLname'=>$req->glname,
                    'guestAddress'=>$req->gaddress,
                    'guestEmail'=>$req->gemail,
                    'contactNo'=>$req->gcontact,
                    'dob'=>$req->gdob,
                    'created_by'=>$user->id,
                    'updated_by'=>$user->id,
                    'created_at'=>date("Y-m-d h:i:s"),
                    'updated_at'=>date("Y-m-d h:i:s"),
                ]);


                DB::commit();

                $data = [
                    'status'=>'200',
                    'error_status'=>'0',
                    'msg'=>'guest updated successfully'
                ];

                return redirect()->route('guests_view')->with('status',$data);
                

            }catch(Exception $e){

                DB::rollBack();

                $data = [
                    'status'=>'400',
                    'error_status'=>'2',
                    'msg'=>'Agent update failed'
                ];
                
                
                return redirect('guests_view')->with('status',$data);

            }


        }else{

            $rules = [
                'gpass'=>['required'],
                'gfname'=>['required','string'],
                'glname'=>['required','string'],
                'gemail'=>['required','email:rfc'],
                'gcontact'=>['required'],
            ];
    
            $msg = [
                'gpass.required'=>'please enter a valid guest Passport / NIC No',
                'gfname.required'=>'please provide a guest first name',
                'glname.required'=>'please provide a guest lastname',
                'gemail.required'=>'please provide a vaild email',
                'gcontact.required'=>'please provide a guest contact no',
            ];


            $user = Auth::user();

            $validation = Validator::make($req->all(),$rules,$msg)->validate();

            DB::beginTransaction();

            try{


                Guest::create([
                    'passport_id'=>$req->gpass,
                    'guestFname'=>$req->gfname,
                    'guestLname'=>$req->glname,
                    'guestAddress'=>$req->gaddress,
                    'guestEmail'=>$req->gemail,
                    'contactNo'=>$req->gcontact,
                    'dob'=>$req->gdob,
                    'created_by'=>$user->id,
                    'updated_by'=>$user->id,
                    'created_at'=>date("Y-m-d h:i:s")
                ]);


                DB::Commit();

                $data = [
                    'status'=>'200',
                    'error_status'=>'0',
                    'msg'=>'new guest added successfully'
                ];

                return redirect()->route('guests_view')->with('status',$data);

            }catch(Exception $e){

            DB::rollBack();
            
            

                $data = [
                    'status'=>'400',
                    'error_status'=>'1',
                    'msg'=>'guest add failed'
                ];

                return redirect('guests_view')->with('status',$data);

            }

        }       

    }


    public function validate_guest_passport(Request $req){

        $rows = Guest::where('passport_id','=',$req->guest_pass)->where('id','!=',$req->guest_id)->exists();
    
        return response()->json($rows);

    }

    public function validate_guest_email(Request $req){

        $rows = Guest::where('guestEmail','=',$req->gemail)->where('id','!=',$req->guest_id)->exists();
    
        return response()->json($rows);

    }


    public function deleteGuest(Request $req){

        try {

            $reservationsExists = RoomReservation::where('guest_id','=',$req->guest_id)->exists();
            $bookingExists = RoomBookingReservation::where('guest_id','=',$req->guest_id)->exists();

            if($reservationsExists||$bookingExists){

                $data = [
                    'status'=>500,
                    'error_status'=>1,
                    'msg'=>'reservations or bookings exists for this user, unable to delete this guest',
                ];

                return response()->json($data);

            }else{

                DB::beginTransaction();

                    GuestRoom::where('guest_id','=',$req->guest_id)->delete();
                    Guest::where('id','=',$req->guest_id)->delete();

                DB::commit();

                $data = [
                    'status'=>200,
                    'error_status'=>0,
                    'msg'=>'guest deleted successfully',
                ];

                return response()->json($data);

            }
            
        } catch (Exception $e) {
           
            DB::rollBack();

            $data = [
                'status'=>500,
                'error_status'=>1,
                'msg'=>'unable to delete guests',
            ];

            return response()->json($data);

        }

    }

}
