<?php

namespace Modules\PrimaryModule\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Modules\PrimaryModule\Models\Room_type; 
use Modules\PrimaryModule\Models\Room;
use Modules\PrimaryModule\Models\Additional_facilities;
use Modules\PrimaryModule\Models\AddAdditionalFacilites;
use Modules\PrimaryModule\Models\Room_Categories;
use Modules\PrimaryModule\Models\Room_cat_amount;
use Modules\PrimaryModule\Models\RoomBookingAllocation;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Session; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Modules\PrimaryModule\Models\Roomallocation;
use Modules\PrimaryModule\Models\RoomRate;
use PhpParser\Node\Stmt\Else_;
use App\Models\User;
use function App\FormatDateTime;
use Yajra\DataTables\DataTables;

class Room_Controller extends Controller
{


    public function change_status_room_category(Request $request)
    {
        $id=$request->id;

        try {

        $Room_type=Room_Categories::where('room_categories_id', $id)->first();
        //dd($Room->Status);
         if($Room_type->status == 1)
         {
            Room_Categories::where('room_categories_id', $id)
            ->update(['status' => 0]);            
            return back()->json([
                'Ok'
            ]);
         }
         if($Room_type->status ==0){
            Room_Categories::where('room_categories_id', $id)
            ->update(['status' => 1 ]);
            return back()->json([
                'Ok'
            ]);
         }
            else{
                return back()->json([
                    'err'
                ]);
                
            }
            
        } catch (Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    public function change_status_room_type(Request $request)
    {
        $id=$request->id;
        try {

        $Room_type=Room_type::where('room_type_id', $id)->first();
        //dd($Room->Status);
         if($Room_type->room_type_status == 1)
         {
            Room_type::where('room_type_id', $id)
            ->update(['room_type_status' => 0]);            
            return back()->json([
                'Ok'
            ]);
         }
         if($Room_type->room_type_status ==0){
            Room_type::where('room_type_id', $id)
            ->update(['room_type_status' => 1 ]);
            return back()->json([
                'Ok'
            ]);
         }
            else{
                return back()->json([
                    'err'
                ]);
                
            }
            
        } catch (Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    public function room_type_delete(Request $request)
    {
        $id=$request->id;
        try {

            $RoomRate=RoomRate::where('room_type_id', $id)->first();
            $Room=Room::where('room_type_id', $id)->first();

            if(!isset($RoomRate) && $RoomRate ==null && !isset($Room) && $Room ==null)
            {
                Room_type::where('room_type_id', $id)->delete();

                $data = [
                        'status'=>'200',
                            'error_status'=>'0',
                            'msg'=>'Room Type Deleted Successfully'
                ];
    
                return redirect()->route('room_type_add_edit')->with('status',$data);

            }
            else
            {
                $data = [
                    'status'=>'400',
                    'error_status'=>'1',
                    'msg'=>'Room Type is used in Room Rates or Rooms'
                ];
    
                return redirect()->route('room_type_add_edit')->with('status',$data);
            }
            
            
        } catch (Exception $e) {
            $data = [
                'status'=>'400',
                'error_status'=>'1',
                'msg'=>'Something Went Wrong'
            ];

            return redirect()->route('room_type_add_edit')->with('status',$data);
        }
    }

    public function room_view_ajax(Request $req){

        $room = room::with(['get_category','RoomTypeWithConcat'])->get()->sortByDesc('room_id');
        return DataTables::of($room) 
        ->addIndexColumn()
        ->addColumn('name', function($row){
            return '<div class="flex justify-center items-center mt-2"><b>'.$row->room_name.'</b></div>';
 
         })
         
        ->addColumn('img', function($row){

            $filepath = public_path('storage/img/rooms/'.$row->room_id.'.jpg');
              
            if(!File::exists($filepath)){

                $val=asset('dist/images/room_defult.jpg');
             
             }else{

                $val=asset('storage/img/rooms/' . $row->room_id. '.jpg');
                
             }

           return "<div class='flex justify-center items-center mt-2'><div class='w-10 h-10 image-fit zoom-in'> <img  class='tooltip rounded-full' src=".$val." title='Uploaded at ".$row->Create_date."'></div>";

        })
        ->addColumn('area', function($row){
           return '<div class="flex justify-center items-center mt-2"><b>'.$row->room_area.' Sqft</b></div>';

        })
        ->addColumn('category', function($row){
            return '<div class="flex justify-center items-center mt-2"><b>'.$row->get_category->room_categories_name.'</b></div>';
 
         })
         ->addColumn('type', function($row){
            return '<div class="flex justify-center items-center mt-2"><b>'.$row->RoomTypeWithConcat->room_type_Select.'</b></div>';
 
         })
         ->addColumn('floor', function($row){
            return '<div class="flex justify-center items-center mt-2"><b>'.$row->room_floor.' Floor</b></div>';
 
         })
        ->addColumn('sts', function($row){
            if ($row->Status == 1) return '<div class="flex justify-center items-center mt-2"><div class="onoffswitch">
            <input onclick="change_status('.$row->room_id.');"  type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="room'.$row->room_id.'" tabindex="0" checked ><label class="onoffswitch-label" for="room'.$row->room_id.'"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label></div> 
            <a style="margin-left: 3%;" href="javascript:;" data-toggle="modal" data-target="#datepicker-modal-preview"><button onclick="getroomdeatils('.$row->room_id.')" class=" text-white  ml-1" type="submit"><i class="fa fa-eye" class="w-4 h-4 mr-1"></i></button></a>
            <a style="margin-left: 3%;" href="room_add_edit?id='.$row->room_id.'" class=" text-white" ><i class="fa fa-edit" aria-hidden="true"></i></a>

            <button style="margin-left: 3%;" onclick="deleteRoom('.$row->room_id.')"class=" text-white  ml-1" type="submit"><i class="fa fa-trash" class="w-4 h-4 mr-1"></i></button>
            </div>';
            if ($row->Status == 0) return '<div class="flex justify-center items-center mt-2"><div  class="onoffswitch">
            <input onclick="change_status('.$row->room_id.');"  type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="room'.$row->room_id.'" tabindex="0" ><label class="onoffswitch-label" for="room'.$row->room_id.'"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label></div>
            <a style="margin-left: 3%;" href="javascript:;" data-toggle="modal" data-target="#datepicker-modal-preview"><button onclick="getroomdeatils('.$row->room_id.')" class=" text-white  ml-1" type="submit"><i class="fa fa-eye" class="w-4 h-4 mr-1"></i></button></a>
            <a style="margin-left: 3%;" href="room_add_edit?id='.$row->room_id.'" class=" text-white" ><i class="fa fa-edit" aria-hidden="true"></i></a>
            <button style="margin-left: 3%;" onclick="deleteRoom('.$row->room_id.')"class=" text-white  ml-1" type="submit"><i class="fa fa-trash" class="w-4 h-4 mr-1"></i></button>
            </div>';

        })


        ->rawColumns(['name','img','area','category','type','floor','sts'])
        ->make(true);

    }

    public function RoomTypeView(Request $request)
    {

        $request['pagenames'] = [
            [
                'displayname'=>'Room Type',
                'routename'=>'room_type_view'
            ],
  
        ];
 
        $room_catogory= Room_Categories::all()->sortByDesc('room_category_details_id');
        $room_types = Room_type::all()->sortByDesc('room_type_id');
        $request['room_types'] = $room_types;
        $request['room_catogory'] = $room_catogory;
        return view('primarymodule::pages/room_type_view',$request);
    }


    public function deleteRoom(Request $request)
    {
        $id=$request->id;
        try {

            $Roomallocation=Roomallocation::where('roomNumber', $id)->first();
            $RoomBookingAllocation=RoomBookingAllocation::where('roomNumber', $id)->first();

            if(!isset($Roomallocation) && $Roomallocation ==null && !isset($RoomBookingAllocation) && $RoomBookingAllocation ==null)
            {
                room::where('room_id', $id)->delete();
                Additional_facilities::where('room_id', $id)->delete();
                return back()->json([
                    'Ok'
                ]);

            }
            else{
                dd('xx');
                
            }
            
        } catch (Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    public function change_status(Request $request)
    {
        $id=$request->id;
        try {

        $Room=room::where('room_id', $id)->first();
        //dd($Room->Status);
         if($Room->Status == 1)
         {
            room::where('room_id', $id)
            ->update(['Status' => 0]);            
            return back()->json([
                'Ok'
            ]);
         }
         if($Room->Status ==0){
            room::where('room_id', $id)
            ->update(['Status' => 1 ]);
            return back()->json([
                'Ok'
            ]);
         }
            else{
                return back()->json([
                    'err'
                ]);
                
            }
            
        } catch (Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    public function getroomdeatils(Request $request)
    {
        $id = $request->id;

        $facility = Additional_facilities::where('room_id', $id)->with('get_facilities')->get();

   
                $room_add_fac[]="";
                foreach($facility as $row)
                {
            
                    $room_add_fac[]=$row->get_facilities[0]->add_additional_facilites_name;
           
           
                }
            

        $room = room::with(['RoomTypeWithConcat','RoomCatgoryWithConcat'])->find($id);

        $cat_id=$room->room_category;
        $room_type_id=$room->room_type_id;
        $Room_Categories=Room_Categories::where('room_categories_id', $cat_id)->get()->first();
        $Room_type = Room_type::where('room_type_id', $room_type_id)->get()->first();

        $req['room'] = $room;
        $req['Room_Categories'] = $Room_Categories;
        $req['Room_type'] = $Room_type;
        $req['room_add_fac'] = $room_add_fac;
        return response()->JSON($req);
    }

    // this function will View add/update the relvant details of the room type to add/update

    public function View_Room_Type_Update_Edit(Request $request)
    {
        $request['symble'] =Session::get('symbolC');
        
        $request['pagenames'] = [
            [
                'displayname'=>'Rooms',
                'routename'=>'room_type_view'
            ],

            [
                'displayname'=>'Add / Edit Room Type',
                'routename'=>'room_type_add_edit'
            ],
  
        ];
        $request['pre_link'] = "room_type_view";
        
        
        if(isset($request->id)){ 
            
            try{
           
                $get_catgory_amount = Room_cat_amount::with(['get_catgory_amount'])->where('room_type_id',$request->id)->get();
                // dd($get_catgory_amount);
                $room_catogory_withid= Room_cat_amount::where('room_type_id',$request->id)->get()->pluck('room_categories_id')->toarray();
                $room_catogory_withamount= Room_cat_amount::where('room_type_id',$request->id)->get()->pluck('room_cat_amounts_amount')->toarray();        
                $room_catogory= Room_Categories::all()->sortByDesc('room_category_details_id');
                $room_types = Room_type::all()->sortByDesc('room_type_id');
                $room_type = room_type::find($request->id);

                $details = Room_type::with(['created_user','updated_user'])->where([['room_type_id', $request->id]])->first();

                if($details->room_type_status == '1')
                {
                    $val='Active';
                }
                else
                {
                    $val='Inactive';
                }
                
                $request['status_info'] = array('status' => $val, 'created_by' => $details->created_user->username, 'created_at' => FormatDateTime($details->Create_date), 'updated_by' => $details->updated_user->username, 'updated_at' => FormatDateTime($details->Update_date));
                $request['details'] = $details;

                $request['room_type'] = $room_type;
                $request['room_types'] = $room_types;
                $request['room_catogory_withid'] = $room_catogory_withid;
                $request['room_catogory'] = $room_catogory;
                $request['room_catogory_withamount'] = $room_catogory_withamount;
                $request['get_catgory_amount'] = $get_catgory_amount;

   
                
               return view('primarymodule::pages/room_type_add_edit', $request);
   
            }catch(Exception $e){
               //dd($e);
                $data = [
                   'status'=>'400',
                   'error_status'=>'1',
                   'msg'=>'Something Went Wrong'
               ];
   
               return redirect('room_type_view')->with('status',$data);
            }
        }else{
               // if there is no req->id then it's a new entry so return a empty form
               $room_catogory= Room_Categories::all()->sortByDesc('room_category_details_id');
               $room_types = Room_type::all()->sortByDesc('room_type_id');
               $request['room_catogory'] = $room_catogory;
               $request['room_types'] = $room_types;
               return view('primarymodule::pages/room_type_add_edit',$request);
   
           }

    }


    // this function will add/update the relvant details of the room type to add/update

    public function Room_Type_Add_Update(Request $request)
    {

        

            $rules = [

                'room_type_select' => 'required',

            ];
            $customMessages = [

                'room_type_select.required' => 'Select the Room Type',

            ];

            $validation = Validator::make($request->all(), $rules, $customMessages);

            $room_type_id = $request->input('room_type_id');
            $room_type_select = $request->input('room_type_select');
            $room_type_descrption = $request->input('room_type_discription');
            $room_type_add_user_id = "1";
            $room_type_add_time = Carbon::now();
            $roomcatidamout=$request->input('amountwithout');
            $room_catid = $request->input('checked_box');
            $room_cat_amount = $request->input('amount');
            $sts='1';

            if ($validation->fails()) {
                $data = [
                    'status'=>'400',
                    'error_status'=>'2',
                    'msg'=>'Validation Failed'
                ];                

                   return back()->with('status',$data);
            } else {


                if ($room_type_id != null && $room_type_id != "") {

                    try{


                        room_type::where('room_type_id', $room_type_id)
                        ->update(['room_type_Select' => $room_type_select,'room_type_descrption' => $room_type_descrption, 'room_type_status' => $sts,'updated_by' => $room_type_add_user_id, 'updated_at' => $room_type_add_time,]);
      
                       $index=0;
                       if($room_catid != null){
                        foreach($room_catid as $rowcat_id_id)
                        {

                            $row['room_type_id']=$room_type_id;
                            $row['room_categories_id']=$rowcat_id_id;
                            $row['room_cat_amounts_amount']=$room_cat_amount[$index];
                            $rommetype[]= $row;
                            $index++;


                        }
                        
                        Room_cat_amount::where('room_type_id', '=', $room_type_id)->delete();
                        Room_cat_amount::insert($rommetype);
                    }
                        $data = [
                            
                            'status'=>'200',
                            'error_status'=>'0',
                            'msg'=>'Room Type Updated Successfully'
                        ];
                        return redirect()->route('room_type_add_edit')->with('status',$data);


                    }catch (QueryException $e) {
                      //dd($e);
                        $data = [
                            
                            'status'=>'200',
                            'error_status'=>'1',
                            'msg'=>'Room Type Update Failed'
                        ];
                        return redirect()->route('room_type_add_edit')->with('status',$data);
                    }
                    catch(Exception $e){
                                
                        // if something went wrong
            
                        $data = [
                            'status'=>'400',
                            'error_status'=>'2',
                            'msg'=>'Room Type Update Failed'
                        ];
            
                        return redirect()->route('room_type_add_edit')->with('status',$data);
            
                    }
                } else {

                    try{


                       $getjobtypeid= room_type::insertGetId(['room_type_Select' => $room_type_select,'room_type_descrption' => $room_type_descrption, 'room_type_status' => $sts,'created_by' => $room_type_add_user_id, 'created_at' => $room_type_add_time,'updated_by' => $room_type_add_user_id, 'updated_at' => $room_type_add_time]);

                       $index=0;
                       if($room_catid != null){
                        foreach($room_catid as $rowcat_id_id)
                        {
                            $row['room_type_id']=$getjobtypeid;
                            $row['room_categories_id']=$rowcat_id_id;
                            $row['room_cat_amounts_amount']=$roomcatidamout[$index];
                            $rommrate[]= $row;
                            $index++;


                        }
                        Room_cat_amount::insert($rommrate);
                    }
                    $data = [
                        'status'=>'200',
                        'error_status'=>'0',
                        'msg'=>'Room Type Added Successfully'
                    ];
                    return redirect()->route('room_type_add_edit')->with('status',$data);
                    }catch (QueryException $e) {
                   
                        $data = [
                            'status'=>'200',
                            'error_status'=>'1',
                            'msg'=>'Room Type Add Failed'
                        ];
                        return redirect()->route('room_type_add_edit')->with('status',$data);
                    }
                    catch(Exception $e){
                          
                        dd($e);
                        // if something went wrong
            
                        $data = [
                            'status'=>'400',
                            'error_status'=>'2',
                            'msg'=>'Room Type Add Failed'
                        ];
            
                        return redirect()->route('room_type_add_edit')->with('status',$data);
            
                    }
                }
            }
    }

    // this function will change the status of the room type
    public function ChangeRoomTypeStatus(Request $request)
    {
        try {

            $id = room_type::where('room_type_status', $request->id)->get();
            //dd($id);
            return back()->json([
                'Ok'
            ]);
        } catch (Exception $e) {
            return response()->json($e->getMessage());
        }
    }
    // this function will Delete the relvant details of the room type
    public function Delete_Room_Type(Request $request)
    {
        try {

            room_type::where('room_type_id', $request->id)->delete();

            return back()->json([
                'Ok'
            ]);
        } catch (Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    public function RoomView(Request $request)
    {

        $request['pagenames'] = [
            [
                'displayname'=>'Rooms',
                'routename'=>'room_view'
            ],
  
        ]; 

        $room = room::with(['get_category'])->get()->sortByDesc('room_id');
        $request['room'] = $room;
        return view('primarymodule::pages/room_view',$request);
    }
    // this function will View add/update the relvant details of the room type to add/update
    public function View_Room_ADD_Edit(Request $request)
    {
        $request['pagenames'] = [
            [
                'displayname'=>'Rooms',
                'routename'=>'room_view'
            ],

            [
                'displayname'=>'Add / Edit Room',
                'routename'=>'room_type_add_edit'
            ],
  
        ];
        $request['pre_link'] = "room_view";
        try {

            if ($request->filled('id')) { 
                $room_id = $request->id;
                $room_type = room_type::all();
                $room_add_fac = Additional_facilities::where([['room_id', $room_id]])->get()->pluck('facilities')->toArray();
                $room = room::with(['RoomTypeWithConcat','RoomCatgoryWithConcat'])->find($room_id);
                $Additional_facilities = AddAdditionalFacilites::all();
                $room_categories =Room_Categories::all();
                $details = room::with(['created_user','updated_user'])->where([['room_id', $room_id]])->first();

                if($details->Status == '1')
                {
                    $val='Active';
                }
                else
                {
                    $val='Inactive';
                }
                $request['status_info'] = array('status' => $val, 'created_by' => $details->created_user->username, 'created_at' => FormatDateTime($details->Create_date), 'updated_by' => $details->updated_user->username, 'updated_at' => FormatDateTime($details->Update_date));


                
                $filepath = public_path('storage/img/rooms/'.$room->room_id.'.jpg');
              
                if(!File::exists($filepath)){

                $img_path=asset('dist/images/room_defult.jpg');
             
                }else{

                $img_path=asset('storage/img/rooms/' . $room->room_id. '.jpg');
                
                }
                $request['img_path'] = $img_path;
                $request['room'] = $room;
                $request['room_type'] = $room_type;
                $request['room_add_fac'] = $room_add_fac;
                $request['Additional_facilities'] = $Additional_facilities;
                $request['room_categories'] = $room_categories;
                $request['details'] = $details;

            } else {
                $room_type = room_type::all();
                $Additional_facilities = AddAdditionalFacilites::all();
                $room_categories =Room_Categories::all();
                $request['room_type'] = $room_type;
                $request['Additional_facilities'] = $Additional_facilities;
                $request['room_categories'] = $room_categories;
                //dd($request);
            }
            return view('primarymodule::pages/room_add_edit', $request);
        } catch (Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    public function room_view_add_edit(Request $request)
    {
        $request['pagenames'] = [
            [
                'displayname'=>' Rooms',
                'routename'=>'room_view'
            ],

            [
                'displayname'=>'View Room',
                'routename'=>'room_view_add_edit'
            ],
  
        ];
        $request['pre_link'] = "room_view";
        try {
 
            if ($request->filled('id')) { 
                $room_id = $request->id;
                $room_type = room_type::all();
                $room_add_fac = Additional_facilities::where([['room_id', $room_id]])->get()->pluck('facilities')->toArray();
                $room = room::with(['RoomTypeWithConcat','RoomCatgoryWithConcat'])->find($room_id);
                $Additional_facilities = AddAdditionalFacilites::all();
                $room_categories =Room_Categories::all();
                $request['room'] = $room;
                $request['room_type'] = $room_type;
                $request['room_add_fac'] = $room_add_fac;
                $request['Additional_facilities'] = $Additional_facilities;
                $request['room_categories'] = $room_categories;
                $status_info = room::with(['created_user','updated_user'])->where([['room_id', $room_id]])->first();
                $request['status_info'] = $status_info;
            } else {
                $room_type = room_type::all();
                $Additional_facilities = AddAdditionalFacilites::all();
                $room_categories =Room_Categories::all();
                $request['room_type'] = $room_type;
                $request['Additional_facilities'] = $Additional_facilities;
                $request['room_categories'] = $room_categories;
                //dd($request);
            }
            return view('primarymodule::pages/room_view_add_edit', $request);
        } catch (Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    // this function will add/update the relvant details of the room add/update

    public function Room_Add_Update(Request $request)
    {

        try {

            $rules = [
                'room_name' => 'required',
                'room_cat' => 'required',
                'rt_id' => 'required',
                'room_area' => 'required',
                'room_max_rec' => ['required', 'numeric'],
                'room_def_red' => ['required', 'numeric'],
                'room_max_adult' => ['required', 'numeric'],
                'room_max_child' => ['required', 'numeric'],
                'room_max_addi_beds' => ['required', 'numeric'],
                'room_floor' => ['required', 'numeric'],
                //'room_descr' => 'required',
                // 'room_img' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:12048'
            ];
            $customMessages = [
                'room_name.required' => 'Room Name Required',
                'room_cat.required' => 'Select the Room Category',
                'rt_id.required' => 'Select the Room Type',
                'room_area.required' => 'Enter the Area',
                'room_max_rec.required' => 'Max residence Required',
                'room_max_rec.numeric' => 'Max residence need to be Numeric',
                'room_def_red.required' => 'Default residence Required',
                'room_def_red.numeric' => 'Default residence need to be Numeric',
                'room_max_adult.required' => 'Max adults is Required',
                'room_max_adult.numeric' => 'Max adults need to be Numeric',
                'room_max_child.required' => 'Max children is Required',
                'room_max_child.numeric' => 'Max children need to be Numeric',
                'room_max_addi_beds.required' => 'Max Additional Beds is Required',
                'room_max_addi_beds.numeric' => 'Max Additional Beds need to be Numeric',
                'room_floor.required' => 'Floor Number is Required',
                'room_floor.numeric' => 'Floor Number need to be Numeric',
                //'room_descr.required' => 'Description Required',
                // 'room_img.required' => 'Image required and Need be JPG fromat'

            ];

            $validatedData = Validator::make($request->all(), $rules, $customMessages);

            if ($validatedData->fails()) {
                return redirect()->back()->withErrors($validatedData)->withInput();
            } else {

                //input values
                $id = $request->input('room_id');
                $room_type_id = $request->input('room_type_id');
                $room_name = $request->input('room_name');
                $room_area = $request->input('room_area');
                $room_category = $request->input('room_cat');
                $room_Type = $request->input('rt_id');
                $room_default_recident = $request->input('room_def_red');
                $room_max_recident = $request->input('room_max_rec');
                $room_max_adults = $request->input('room_max_adult');
                $room_max_children = $request->input('room_max_child');
                $room_beds = $request->input('room_max_addi_beds');
                $room_floor = $request->input('room_floor');
                $room_status = "1";
                $room_descrption = $request->input('room_descr');
                $room_add_user = "kasun";
                $room_add_time = Carbon::now();
                $room_type_id = $request->input('room_type_id');
                $addtiona=$request->input('facilities');
                $addtiona_keep=$request->input('checked_box');
                //checkbox values
                $checked_box = $request->input('checked_box');
                //dd($checked_box);
                $form_status = $request->input('form_status');
                $user=Auth::user()->id;
                if ($id != null && $id != "") {



                    room::where('room_id', $id)
                        ->update([
                            'room_name' => $room_name, 'room_area' => $room_area, 'room_category' => $room_category,  'room_default_recident' => $room_default_recident, 'room_max_recident' => $room_max_recident, 'room_max_adults' => $room_max_adults, 'room_max_children' => $room_max_children, 'room_beds' => $room_beds, 'room_floor' => $room_floor, 'room_status' => $room_status, 'room_descrption' => $room_descrption, 'room_type_id' => $room_Type, 'Status' => $form_status, 'Update_date' => $room_add_time, 'update_by' => $user,
                        ]);

                    Additional_facilities::where('room_id', $id)->delete();

                    if(isset($addtiona))
                    {
                        foreach($addtiona as $row) 
                        {

                            $add_fac_id= AddAdditionalFacilites::insertGetId(['add_additional_facilites_name'=>$row]);
                            Additional_facilities::insert(['room_id' => $id,'facilities'=>$add_fac_id]);
                        }
                        if($addtiona_keep){
                        foreach($addtiona_keep as $row) 
                        {
                            Additional_facilities::insert(['room_id' => $id,'facilities'=>$row]);
                        }
                    }
                        
                    }else
                    {
                        if($addtiona_keep){
                        foreach($addtiona_keep as $row) 
                        {
                            Additional_facilities::insert(['room_id' => $id,'facilities'=>$row]);
                        }
                    }
                    }
                    
                    
                    $image_path = "'storage/img/rooms/' . $id . '.jpg'";  // Value is not URL but directory file path
                    if (File::exists($image_path)) {
                        File::delete($image_path);
                    }
                    $imageName = $id . '.jpg';
                } else {


                    $id = room::insertGetId([
                        'room_name' => $room_name, 'room_area' => $room_area, 'room_category' => $room_category,  'room_default_recident' => $room_default_recident, 'room_max_recident' => $room_max_recident, 'room_max_adults' => $room_max_adults, 'room_max_children' => $room_max_children, 'room_beds' => $room_beds, 'room_floor' => $room_floor, 'room_status' => $room_status, 'room_descrption' => $room_descrption,'room_type_id' => $room_Type, 'Status' => '1', 'Create_date' => $room_add_time, 'Update_date' => $room_add_time, 'create_by' => $user, 'update_by' => $user,
                    ]);


                    if(isset($addtiona))
                    {
                        foreach($addtiona as $row) 
                        {

                            $add_fac_id= AddAdditionalFacilites::insertGetId(['add_additional_facilites_name'=>$row]);
                            Additional_facilities::insert(['room_id' => $id,'facilities'=>$add_fac_id]);
                        }
                        if($addtiona_keep){
                        foreach($addtiona_keep as $row) 
                        {
                            Additional_facilities::insert(['room_id' => $id,'facilities'=>$row]);
                        }
                        }
                        
                    }else
                    {
                        if($addtiona_keep){
                        foreach($addtiona_keep as $row) 
                        {
                            Additional_facilities::insert(['room_id' => $id,'facilities'=>$row]);
                        }
                    }
                    }

                    $imageName = $id . '.jpg';
                }
                if($request->room_img!=0){
                $request->room_img->move(public_path('storage/img/rooms'), $imageName);
                }
                
                $data = [ 
                    'status'=>'200',
                    'error_status'=>'0',
                    'msg'=>'Room Added Successfully'
                ];
                return redirect()->route('room_view')->with('status',$data);

            }
         }catch (QueryException $e) {
            dd($e);   
                        $data = [
                            'status'=>'200',
                            'error_status'=>'1',
                            'msg'=>'Room Added Successfully'
                        ];
                        return redirect()->route('room_add_edit')->with('status',$data);
                    }
                    catch(Exception $e){
                            dd($e);        
                    
            
                       dd($e);
                        $data = [
                            'status'=>'400',
                            'error_status'=>'2',
                            'msg'=>'Room Adding Failed'
                        ];
            
                        return redirect('room_add_edit')->with('status',$data);
            
                    }
    }
    public function AddAdditionalFacilites(Request $request)
    {
        try {
            $addtional_facility = $request->input('addtoonal');
            AddAdditionalFacilites::insert(['add_additional_facilites_name' => $addtional_facility]);
            return back();
        } catch (Exception $e) {
            return response()->json($e->getMessage());
        }
    }
 
    public function View_Room_Catagory_Update_Edit(Request $request)
    {
        
        $request['pagenames'] = [

            [
                'displayname'=>'Add / Edit Room Category',
                'routename'=>'room_category_view_add_update'
            ],
  
        ];
        $request['pre_link'] = "room_category_view_add_update";
        
        if(isset($request->id)){ 

            try{

                $room_catogory= Room_Categories::all()->sortByDesc('room_categories_id');
                $room_catogory_with_id = Room_Categories::find($request->id);

                $details = Room_Categories::where([['room_categories_id', $request->id]])->first();
                $created_by=User::where([['id',  $details->created_by]])->first();
                $updated_by=User::where([['id',  $details->updated_by]])->first();
                
                if($details->status == '1')
                {
                    $val='Active';
                }
                else
                {
                    $val='Inactive';
                }
                
                $request['status_info'] = array('status' => $val, 'created_by' => $created_by->username, 'created_at' => FormatDateTime($details->Create_date), 'updated_by' => $updated_by->username, 'updated_at' => FormatDateTime($details->Update_date));
                $request['details'] = $details;

                $request['room_catogory_withid'] = $room_catogory_with_id;
                $request['room_catogory'] = $room_catogory;
   
                
               return view('primarymodule::pages/room_category_view_add_update', $request);
   
            }catch(Exception $e){
   
                $data = [
                   'status'=>'400',
                   'error_status'=>'1',
                   'msg'=>'Something Went Wrong'
               ];
   
               return redirect('room_category_view_add_update')->with('status',$data);
            }
        }else{
               // if there is no req->id then it's a new entry so return a empty form
               $room_catogory= Room_Categories::all()->sortByDesc('room_categories_id');
               $request['room_catogory'] = $room_catogory;
               return view('primarymodule::pages/room_category_view_add_update',$request);
   
           }

    }
    public function Room_Category_Add_Update(Request $request)
    {
       
            $rules = [
                // 'cat_name' => 'required',
                // 'area' => 'required',
                // 'max_reci' => 'required',
                // 'defa_rec' => 'required',
                // 'max_adults' => 'required',
                // 'max_child' => 'required'
            ];
            $customMessages = [

                'cat_name.required' => 'Category Name Required',
                'area.required' => 'Area Required',
                'max_reci.required' => 'Max Residence Required',
                'defa_rec.required' => 'Default  Residence Required',
                'max_adults.required' => 'Max adults Required',
                'max_child.required' => 'Max Child Required'
            ];

            $validation = Validator::make($request->all(), $rules, $customMessages);
            
                $cat_id = $request->input('cat_id');
                $cat_name = $request->input('cat_name');
                $area = $request->input('area');
                $max_reci = $request->input('max_reci');
                $defa_rec = $request->input('defa_rec');
                $max_adults = $request->input('max_adults');
                $max_child = $request->input('max_child');
                $img_review = $request->input('images');
               
                $sts = $request->input('form_status');
    
                $user=Auth::user()->id;
                $room_add_time = Carbon::now();
            if ($validation->fails()) {
                $data = [
                    'status'=>'400',
                    'error_status'=>'2',
                    'msg'=>'Validation Failed'
                ];                

                   return back()->with('status',$data);
            } else {

                


                if ($cat_id != null && $cat_id != "") {

                    try{
                        Room_Categories::where('room_categories_id', $cat_id)
                        ->update(['room_categories_name' => $cat_name,'area' => $area, 'max_recident' => $max_reci,'default_recident' => $defa_rec, 'max_adults' => $max_adults, 'max_children' => $max_child,'status'=> $sts,'updated_by'=> $user,'updated_at'=> $room_add_time]);
                        $data = [
                            'status'=>'200',
                            'error_status'=>'0',
                            'msg'=>'Room Categories Updated Successfully'
                        ];
                        

                        try{

                            $TEmp_two = public_path('storage/img/temp');
                            if(!File::exists($TEmp_two)) {
                            File::makeDirectory($TEmp_two);
                            }

                            $fileNames = [];
                            $path_two = public_path('storage/img/temp');
                             $files = File::allFiles($path_two);
                            foreach($files as $file) {
                            array_push($fileNames, pathinfo($file)['filename']);
                            }
    
    
                            foreach($fileNames as $filename)
                            {
                                $foldercheck = public_path('storage/img/Room_Category/'.$cat_id);
                                if(!File::exists($foldercheck)) {
                                    File::makeDirectory($foldercheck);
                                    }
                                File::move(public_path('storage/img/temp/'.$filename.'.jpg'),public_path('storage/img/Room_Category/'.$cat_id.'/'.$filename.'.jpg'));
                            }
    
                            }
                            catch(Exception $e)
                            {
                                dd($e);
                            }

                            return redirect()->route('room_category_view_add_update')->with('status',$data);
                    }catch (QueryException $e) {
                      dd( $e);
                        $data = [
                            
                            'status'=>'200',
                            'error_status'=>'1',
                            'msg'=>'Room Categories Updated Unsuccessfull'
                        ];
                        return redirect()->route('room_category_view_add_update')->with('status',$data);
                    }
                    catch(Exception $e){
                                    
                        // if something went wrong
            
                        $data = [
                            'status'=>'400',
                            'error_status'=>'2',
                            'msg'=>'Room Categories Update Failed'
                        ];
            
                        return redirect('room_category_view_add_update')->with('status',$data);
            
                    }
                } else {

                    try{


                        $folder_id=Room_Categories::insertGetId(['room_categories_name' => $cat_name,'area' => $area, 'max_recident' => $max_reci,'default_recident' => $defa_rec, 'max_adults' => $max_adults, 'max_children' => $max_child,'status'=>1,'created_by'=> $user,'created_at'=> $room_add_time,'updated_by'=> $user,'updated_at'=> $room_add_time]);

                        $path = public_path('storage/img/Room_Category/'.$folder_id);
                        File::makeDirectory($path, 0777, true, true);

                    try{
                        $TEmp_two = public_path('storage/img/temp');
                        if(!File::exists($TEmp_two)) {
                            
                        }
                        $fileNames = [];
                        $path_two = public_path('storage/img/temp');
                         $files = File::allFiles($path_two);
                        foreach($files as $file) {
                        array_push($fileNames, pathinfo($file)['filename']);
                        }


                        foreach($fileNames as $filename)
                        {
                            File::move(public_path('storage/img/temp/'.$filename.'.jpg'),public_path('storage/img/Room_Category/'.$folder_id.'/'.$filename.'.jpg'));
                        }

                        }
                        catch(Exception $e)
                        {
                            dd($e);
                        }



                    $data = [
                        'status'=>'200',
                        'error_status'=>'0',
                        'msg'=>'Room Categories Add Successfully'
                    ];
                    return redirect()->route('room_category_view_add_update')->with('status',$data);
                    }catch (QueryException $e) {
                       dd($e);
                        $data = [
                            'status'=>'200',
                            'error_status'=>'1',
                            'msg'=>'Room Categories Add Successfully'
                        ];
                        return redirect()->route('room_category_view_add_update')->with('status',$data);
                    }
                    catch(Exception $e){
                               dd($e);     
                        // if something went wrong
            
                        $data = [
                            'status'=>'400',
                            'error_status'=>'2',
                            'msg'=>'Room Categories Adding Failed'
                        ];
            
                        return redirect('room_category_view_add_update')->with('status',$data);
            
                    }
                }
            }
    }

    public function room_category_delete(Request $request)
    {
        $id=$request->id;
        try {

            $RoomRate=RoomRate::where('room_category', $id)->first();
            $Room=Room::where('room_category', $id)->first();

            if(!isset($RoomRate) && $RoomRate ==null && !isset($Room) && $Room ==null)
            {
                Room_Categories::where('room_categories_id', $id)->delete();

                $data = [
                        'status'=>'200',
                            'error_status'=>'0',
                            'msg'=>'Room categories delete successful'
                ];
    
                return redirect()->route('room_category_view_add_update')->with('status',$data);

            }
            else
            {
                $data = [
                    'status'=>'400',
                    'error_status'=>'1',
                    'msg'=>'Room categories is used in room rates or rooms'
                ];
    
                return redirect()->route('room_category_view_add_update')->with('status',$data);
            }
            
            
        } catch (Exception $e) {
            $data = [
                'status'=>'400',
                'error_status'=>'1',
                'msg'=>'Something went wrong'
            ];

            return redirect()->route('room_type_add_edit')->with('status',$data);
        }
    }

    
    
    // this will return the room additional facilities crud view

    public function room_facilities_view(Request $req){

        $req['pagenames'] = [
            [
                'displayname'=>'Room Facilities',
                'routename'=>'room_facilities_view'
            ],
  
        ];
        
        return view('primarymodule::pages/room_additional_features',$req);

    }


    public function getAllRoomFacilities(Request $req){
        
        $all_facilities = AddAdditionalFacilites::all();

        return DataTables()::of($all_facilities)
        ->addIndexColumn()
        ->addColumn('edit-btn', function($row){

            return '<div class="flex justify-center items-center mt-2"><a  href="#" style="margin-top: 0%" class="flex items-center mr-3 text-theme-1" onclick="get_facility('.$row->add_additional_facilites_id.')" ><i class="fas fa-edit"></i></a><a  href="#" style="margin-top: 0%" class="flex items-center mr-3 text-theme-1" onclick="deleteFacility('.$row->add_additional_facilites_id.')" ><i class="fas fa-trash"></i></a></div>';
        
        })

        ->rawColumns(['edit-btn'])
        ->make(true);


    }


    public function getFacility(Request $req){

        try {

            $facility = AddAdditionalFacilites::where('add_additional_facilites_id','=',$req->facility_id)->first();

            $data = [
                'status'=>200,
                'error_status'=>0,
                'msg'=>'Data Fetched Successfully',
                'data'=>$facility,
            ];


            return response()->json($data);
          
        } catch (\Throwable $th) {
           
            $data = [
                'status'=>500,
                'error_status'=>1,
                'msg'=>'Unable to Fetch Facility',
            ];

            return response()->json($data);

        }

    }


    public function deleteFacility(Request $req){

        try {

            // this will check whether there are rooms this facility is added, then shown error else delete

            $checkRoomFacilities = Additional_facilities::where('facilities','=',$req->facility_id)->exists();
         
            if($checkRoomFacilities){

                $data = [
                    'status'=>500,
                    'error_status'=>1,
                    'msg'=>'Unable to Delete Facility due to this Facility is used by Rooms',
                ];

                return response()->json($data);

            }else{

                AddAdditionalFacilites::where('add_additional_facilites_id','=',$req->facility_id)->delete();

                $data = [
                    'status'=>200,
                    'error_status'=>0,
                    'msg'=>'Facility Deleted Successfully',
                ];

                return response()->json($data);

            }

        } catch (Exception $e) {
            
            $data = [
                'status'=>500,
                'error_status'=>1,
                'msg'=>'Something Went Wrong, Unable to Delete Facility',
                'error_msg'=>$e->getMessage(),
            ];

            return response()->json($data);

        }

    }


    // this will add or update a new facility to room list

    public function add_update_facilities(Request $req){

        $rules = [
            'facility_name'=>'required'
        ];


        $msg = [
            'facility_name.required'=>'Please enter a name to the Facility'
        ];

        $validatedData = Validator::make($req->all(), $rules,$msg)->validate();    
        
        if($req->facility_id_holder!=null){

            try{

                AddAdditionalFacilites::where([
                    ['add_additional_facilites_id',$req->facility_id_holder]
                ])->update(['add_additional_facilites_name' => $req->facility_name]);

                
                $data = [
                    'status'=>'200',
                    'error_status'=>'0',
                    'msg'=>'Facility Updated Successfully'
                ];
        
                return redirect()->route('room_facilities_view')->with('status',$data);

            }catch(Exception $e){

                $data = [
                    'status'=>'400',
                    'error_status'=>'2',
                    'msg'=>'Season update failed'
                ];

                return redirect('room_facilities_view')->with('status',$data);
            }

        }else{

            try{
 
                AddAdditionalFacilites::insert(['add_additional_facilites_name' => $req->facility_name]);

                $data = [
                    'status'=>'200',
                    'error_status'=>'0',
                    'msg'=>'New Facility Added Successfully'
                ];

              return redirect()->route('room_facilities_view')->with('status',$data);


            }catch(Exception $e){

                $data = [
                    'status'=>'400',
                    'error_status'=>'2',
                    'msg'=>'Facility Adding Failed'
                ];

                return redirect('room_facilities_view')->with('status',$data);

            }

        }

    }


    
    public function validate_facility_name(Request $req){

        $rows = AddAdditionalFacilites::where('add_additional_facilites_name','=',$req->gfacility)->where('add_additional_facilites_id','!=',$req->facility_id)->exists();

        return response()->json($rows);

    }


    public function Img_Uplord(Request $req){

        $img = $req->file('file');
        $imageName=time().'.'.'jpg';
        $req->file->move(public_path('storage/img/temp'), $imageName);

        // $fileNames = [];
        // $path = public_path('storage/img/temp');
        // $files = File::allFiles($path);
        // foreach($files as $file) {
        //     array_push($fileNames, pathinfo($file)['filename']);
        // }
        // dd($fileNames);
  


    }
    public function Fetch_Img()
    {
        $fileNames = [];
        $path = public_path('storage/img/temp');
        $files = File::allFiles($path);
        foreach($files as $file) {
            array_push($fileNames, pathinfo($file)['filename']);
        }

        $output_array=[];
        foreach($fileNames as $filename)
        {
            $imgpath=asset('storage/img/temp/'.$filename.'.jpg');
            $output ='<div class="w-24 h-24 relative image-fit mb-5 mr-5 cursor-pointer zoom-in"><img class="rounded-md remove_img" id="'.$filename.'" alt=""  src="'.$imgpath.'"><div title="click on image to delete"class="tooltip w-5 h-5 flex items-center justify-center absolute rounded-full text-white bg-theme-6 right-0 top-0 -mr-2 -mt-2"><i class="w-4 h-4">X</i> </div></div>';
            $output_array[] = $output;
        }

        return $output_array;
        

    }


    public function Remove_Fetch_Img(Request $request)
    {
        if($request->get('name'))
        {
            $name=$request->get('name');
            File::delete(public_path('storage/img/temp/'.$name.'.jpg'));
        }
        

    }

    public function Edit_Fetch_Img(Request $request)
    {
        $folder_id=$request->get('data');
        $fileNames = [];
        $path = public_path('storage/img/Room_Category/'.$folder_id);
        $files = File::allFiles($path);
        foreach($files as $file) {
            array_push($fileNames, pathinfo($file)['filename']);
        }

        $output_array=[];
        foreach($fileNames as $filename)
        {
            $imgpath=asset('storage/img/Room_Category/'.$folder_id.'/'.$filename.'.jpg');
            $output ='<div class="w-24 h-24 relative image-fit mb-5 mr-5 cursor-pointer zoom-in"><img class="rounded-md remove_img_saved" id="'.$filename.'" alt=""src="'.$imgpath.'"><div title="click on image to delete"class="tooltip w-5 h-5 flex items-center justify-center absolute rounded-full text-white bg-theme-6 right-0 top-0 -mr-2 -mt-2"><i class="w-4 h-4">X</i> </div></div>';
            $output_array[] = $output;
        }

        return $output_array;
        

    }
    public function Saved_Remove_Fetch_Img(Request $request)
    {
        if($request->get('name'))
        {
            $filename=$request->get('name');
            $folder_id=$request->get('folder_id');

            //dd('storage/img/Room_Category/'.$folder_id.'/'.$filename.'.jpg');
            File::delete(public_path('storage/img/Room_Category/'.$folder_id.'/'.$filename.'.jpg'));
        }
        

    }


    public function get_all_rooms(Request $req){
        
        try{

            $rooms = Room::all();

            $data = [
                'status'=>200,
                'error_status'=>0,
                'data'=>$rooms,
            ];

            return response()->json($data);

        }catch(Exception $e){

            $data = [
                'status'=>500,
                'error_status'=>1,
                'msg'=>'Unable to Fetch Rooms',
            ];

            return response()->json($data);

        }

    }


   
}
