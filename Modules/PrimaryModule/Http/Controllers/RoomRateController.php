<?php

namespace Modules\PrimaryModule\Http\Controllers;

use Illuminate\Http\Request;
use Modules\PrimaryModule\Models\RoomRate;
use Modules\PrimaryModule\Models\Season;
use Modules\PrimaryModule\Models\Agent;
use Modules\PrimaryModule\Models\MealPlan;
use Modules\PrimaryModule\Models\Room_type;
use Modules\PrimaryModule\Models\Room_Categories;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Yajra\DataTables\DataTables;

use function App\GetSystemUserCurrency;
use function App\GetSystemUserCurrency_convertion;
use function App\GetSystemUserSymble;
use function App\FormatDateTime;

class RoomRateController extends Controller
{

    public function checkcurrys_rate_agent_room(Request $request)
    {
        $agent_id = $request->agent_id;
        $season_id = $request->season_id;
        try {

            $RoomRate = RoomRate::where('agent_id', $agent_id)->where('season_id', $season_id)->first();
            //dd($Room->Status);
            if ($RoomRate->status == 1) {
                RoomRate::where('agent_id', $agent_id)->where('season_id', $season_id)
                    ->update(['status' => 0]);
                return back()->json([
                    'Ok'
                ]);
            }
            if ($RoomRate->Status == 0) {
                RoomRate::where('agent_id', $agent_id)->where('season_id', $season_id)
                    ->update(['status' => 1]);
                return back()->json([
                    'Ok'
                ]);
            } else {
                return back()->json([
                    'err'
                ]);
            }
        } catch (Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    public function get_all_roomrate(Request $req)
    {

        $all_rates = RoomRate::with(['get_travel_agent', 'get_season', 'get_room_type'])->groupBy('agent_id', 'season_id')->get();

        //dd($all_rates);
        return DataTables::of($all_rates)
            ->addIndexColumn()
            ->addColumn('agent', function ($row) {
                return '<div class="flex justify-center items-center mt-2"><b>' . $row->get_travel_agent->agentName . '</b></div>';
            })
            ->addColumn('season', function ($row) {
                return '<div class="flex justify-center items-center mt-2"><b>' . $row->get_season->seasonName . '</b></div>';
            })
            ->addColumn('action', function ($row) {

                if ($row->status == 1) return '<div class="flex justify-center items-center mt-2">
                <div  class="onoffswitch">
                <input onclick="change_status(' . $row->get_travel_agent->id . ',' . $row->get_season->id . ');"  type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="room' . $row->get_travel_agent->id . '' . $row->get_season->id . '" tabindex="0" checked ><label class="onoffswitch-label" for="room' . $row->get_travel_agent->id . '' . $row->get_season->id . '"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label></div>

                <a style="margin-left: 5%;" href="room_view_rate?id=' . $row->id . '&agentid=' . $row->agent_id . '&seasonid=' . $row->season_id . '&roomtypeid=' . $row->room_type_id . '" class=" text-white" ><i class="fa fa-eye" aria-hidden="true"></i></a>

                <a style="margin-left: 5%;" href="add_update_room_rate_view?id=' . $row->id . '&agentid=' . $row->agent_id . '&seasonid=' . $row->season_id . '&roomtypeid=' . $row->room_type_id . '" class=" text-white" ><i class="fa fa-edit" aria-hidden="true"></i></a>

                <button style="margin-left: 5%;" onclick="deleterate(' . $row->agent_id . ',' . $row->season_id . ')"class=" text-white  ml-1" type="submit"><i class="fa fa-trash" class="w-4 h-4 mr-1"></i></button>

                </div>';
                if ($row->status == 0) return '<div class="flex justify-center items-center mt-2">
                <div  class="onoffswitch">
                <input onclick="change_status(' . $row->get_travel_agent->id . ',' . $row->get_season->id . ');"  type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="room' . $row->get_travel_agent->id . '' . $row->get_season->id . '" tabindex="0" ><label class="onoffswitch-label" for="room' . $row->get_travel_agent->id . '' . $row->get_season->id . '"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label></div>
                <a style="margin-left: 5%;" href="room_view_rate?id=' . $row->id . '&agentid=' . $row->agent_id . '&seasonid=' . $row->season_id . '&roomtypeid=' . $row->room_type_id . '" class=" text-white" ><i class="fa fa-eye" aria-hidden="true"></i></a>

                <a style="margin-left: 5%;" href="add_update_room_rate_view?id=' . $row->id . '&agentid=' . $row->agent_id . '&seasonid=' . $row->season_id . '&roomtypeid=' . $row->room_type_id . '" class=" text-white" ><i class="fa fa-edit" aria-hidden="true"></i></a>

                <button style="margin-left: 5%;" onclick="deleterate(' . $row->agent_id . ',' . $row->season_id . ')"class="text-white  ml-1" type="submit"><i class="fa fa-trash" class="w-4 h-4 mr-1"></i></button>
                </div>';
            })


            ->rawColumns(['agent', 'season', 'action'])
            ->make(true);
    }


    public function agent_room_rate_view(Request $req)
    {

        // this is used in the top page navigation
        // ex : dashboard >> settings >> some other page
        // please provide page name and route name and key value pair in the
        // below associative array

        $params['pagenames'] = [
            [
                'displayname' => 'Room Rates View',
                'routename' => 'agent_room_rate_view'
            ],

        ];


        $Cyrate = GetSystemUserCurrency(1);
        $all_rates = RoomRate::with(['get_travel_agent', 'get_season', 'get_room_type'])->where([['status', '1']])->groupBy('agent_id', 'season_id')->get();
        $params['agent_rates'] = $all_rates;
        $params['symble'] = GetSystemUserSymble();
        $params['Cyrate'] = $Cyrate;
        return view('primarymodule::pages/room_rate_view', $params);
    }


    public function get_all_room_rate(Request $req)
    {

        try {

            $room_type_ids = RoomRate::with(['get_travel_agent', 'get_season', 'get_room_type'])->where([['agent_id', $req->agent_id], ['season_id', $req->season_id]])->groupBy('room_type_id')->pluck('room_type_id')->toarray();

            $mealplan = MealPlan::where([['status', '1']])->get();
            $room_types = Room_type::whereNotIn('room_type_id', $room_type_ids)->get();
            $room_cat = Room_Categories::all();
            $symble = GetSystemUserSymble();
            $index_header = 0;
            foreach ($room_cat as $row2) {
                $top_header[$index_header] = '<th value="' . $row2->room_categories_id . '">' . $row2->room_categories_name . '</th>';
                $index_header++;
            }


            $index_lable = 0;
            foreach ($room_types as $row) {
                $top_lable[$index_lable] = '<input hidden class="border" name="room_type_id_add[]" id="room_type_id_add" style="width: 100%; text-align: right" value="' . $row->room_type_id . '"> 
                <div class="col-span-12 lg:col-span-6">
                <div class="accordion user-permissions-content">
                <div id="modulediv" class="user-permissions accordion__pane  border-b border-gray-200 dark:border-dark-5">
                <div class="intro-y grid grid-cols-12 gap-6 border-b header-row">
                <div class="col-span-12 lg:col-span-9 title-side">
                <label style="font-size: 20px;" class="flex flex-col sm:flex-row">' . $row->room_type_Select . '</label>
                </div>
                <div class="col-span-12 lg:col-span-3 controler-side">
                    <a id="togglerbt" href="javascript:;"
                        class="user-permissions-list accordion__pane__toggle view-more-icon font-medium block">
                        <i style="" class="fas fa-chevron-circle-down"></i>
                    </a>
                </div>
                </div>
                <div class="accordion__pane__content mt-3 text-gray-700 dark:text-gray-600 leading-relaxed"
                id="mo">
                <div class="overflow-x-auto 2nd "><table class="table table-report -mt-2 dttbl' . $index_lable . '" id="dttbl"><thead><tr class="dttbl_tr"><th>Meal plan</th></tr></thead><tbody id="data_table" class="mealplan"></tbody></table></div></div></div></div></div>';
                $index_lable++;
            }

            $paramss['symble']=GetSystemUserSymble();
            $paramss['top_lable']=$top_lable;
            $paramss['index_lable']=$index_lable;
            $paramss['top_header']=$top_header;
            $paramss['room_cat']=$room_cat;
            $paramss['mealplan']=$mealplan;
            
            return response()->json($paramss);
        } catch (Exception $e) {
            $data = [
                'status' => '400',
                'error_status' => '1',
                'msg' => 'Something went wrong'
            ];

            return redirect('agent_room_rate_view')->with('status', $data);
        }
    }

    public function add_update_room_rate_view(Request $req)
    {

        $params['pagenames'] = [
            [
                'displayname' => 'Room Rates View',
                'routename' => 'agent_room_rate_view'
            ],

            [
                'displayname' => 'Add / Edit Room Rate',
                'routename' => 'add_update_room_rate_view'
            ],

        ];


        // send all the seasons to show in the table
        $params['seasons'] = Season::get();
        $params['agents'] = Agent::get();
        $params['mealplan'] = MealPlan::get();
        $params['room_types'] = Room_type::all();
        $params['room_cat'] = Room_Categories::all();

        $Cyrate = GetSystemUserCurrency(1);
        $all_data = RoomRate::with(['get_travel_agent', 'get_season', 'get_room_type', 'get_meal_plan', 'get_room_category'])->get();
        $params['agent_rates'] = $all_data;
        $params['symble'] = GetSystemUserSymble();
        if (isset($req->agentid)) {

            try {

                $all_rates_get = RoomRate::with(['get_travel_agent'])->where([['agent_id', $req->agentid], ['season_id', $req->seasonid]])->get();



                $all_rates_get_room_type = RoomRate::with(['get_travel_agent', 'get_season', 'get_room_type'])->where([['agent_id', $req->agentid], ['season_id', $req->seasonid]])->groupBy('room_type_id')->get();
                //dd($all_rates_get_room_type);

                $details = RoomRate::with(['created_user', 'updated_user'])->where([['agent_id', $req->agentid], ['season_id', $req->seasonid], ['room_type_id', $req->roomtypeid]])->first();

                if ($all_rates_get) {
                    // is there is relvant data then append to the status info
                    $params['Cyrate'] = $Cyrate;
                    $params['all_rates_get_room_type'] = $all_rates_get_room_type;
                    $params['all_rates_get'] = $all_rates_get;
                    if ($details->status == '1') {
                        $val = 'Active';
                    } else {
                        $val = 'Inactive';
                    }

                    $params['status_info'] = array('status' => $val, 'created_by' => $details->created_user->username, 'created_at' => FormatDateTime($details->Create_date), 'updated_by' => $details->updated_user->username, 'updated_at' => FormatDateTime($details->Update_date));
                    $params['details'] = $details;
                }

                return view('primarymodule::pages/room_rate_add_update', $params);
            } catch (Exception $e) {
                $data = [
                    'status' => '400',
                    'error_status' => '1',
                    'msg' => 'Something went wrong'
                ];
                return redirect('agent_room_rate_view')->with('status', $data);
            }
        } else {
            $details = RoomRate::with(['created_user', 'updated_user'])->where([['room_rates.id', $req->id]])->first();
            $params['details'] = $details;
            return view('primarymodule::pages/room_rate_add_update', $params);
        }
    }

    public function room_view_rate(Request $req)
    {

        $params['pagenames'] = [
            [
                'displayname' => 'Room Rates',
                'routename' => 'agent_room_rate_view'
            ],

            [
                'displayname' => 'View Room Rate',
                'routename' => 'add_update_room_rate_view'
            ],

        ];


        // send all the seasons to show in the table
        $params['seasons'] = Season::get();
        $params['agents'] = Agent::get();
        $params['mealplan'] = MealPlan::get();
        $params['room_types'] = Room_type::all();
        $params['room_cat'] = Room_Categories::all();
        $params['symble'] = GetSystemUserSymble();
        $Cyrate = GetSystemUserCurrency(1);
        $params['Cyrate'] = $Cyrate;
        $all_data = RoomRate::with(['get_travel_agent', 'get_season', 'get_room_type', 'get_meal_plan', 'get_room_category'])->get();
        $params['agent_rates'] = $all_data;

        if (isset($req->agentid )) {

            try {

                $all_rates_get = RoomRate::with(['get_travel_agent'])->where([['agent_id', $req->agentid], ['season_id', $req->seasonid]])->get();

                $all_rates_get_room_type = RoomRate::with(['get_travel_agent', 'get_season', 'get_room_type'])->where([['agent_id', $req->agentid], ['season_id', $req->seasonid]])->groupBy('room_type_id')->get();

                $details = RoomRate::with(['created_user', 'updated_user'])->where([['agent_id', $req->agentid], ['season_id', $req->seasonid]])->first();

                if ($all_rates_get) {
                    // is there is relvant data then append to the status info
                    $params['all_rates_get_room_type'] = $all_rates_get_room_type;
                    $params['all_rates_get'] = $all_rates_get;
                    $params['details'] = $details;
                    $params['status_info'] = array('created_by' => $details->created_user->name, 'created_at' => $details->created_at, 'updated_by' => $details->updated_user->name, 'updated_at' => $details->updated_at);
                }

                return view('primarymodule::pages/room_view_rate', $params);
            } catch (Exception $e) {
                $data = [
                    'status' => '400',
                    'error_status' => '1',
                    'msg' => 'Something went wrong'
                ];

                return redirect('agent_room_rate_view')->with('status', $data);
            }
        } else {


            $details = RoomRate::with(['created_user', 'updated_user'])->where([['room_rates.id', $req->id]])->first();
            $params['details'] = $details;
            return view('primarymodule::pages/room_rate_add_update', $params);
        }
    }


    public function agent_rates_add_edit(Request $request)
    {
        //dd($request);

        $agent_id = $request->input('agent_id');
        $season_id = $request->input('season_id');
        //dd($agent_id,$season_id);
        $roomtypeid = $request->input('roomtypeid');
        $get_amount = $request->input('amount');
        $room_type_id_add = $request->input('room_type_id_add');
        $user = Auth::user();

        $season = Season::where([['status', '1']])->get();
        $Agent = Agent::where([['status', '1']])->get();
        $MealPlan = MealPlan::where([['status', '1']])->get();
        $Room_type = Room_type::all();
        $Room_typez = Room_type::where([['room_type_id', $roomtypeid]])->get();
        $Room_Categories = Room_Categories::all();
        $index = 0;
        $Cyrate = GetSystemUserCurrency_convertion(1);

        if (isset($request->roomtypeid) && $request->roomtypeid != '') {

            try {
                //dd($agent_id);
                foreach ($roomtypeid as $roomtypeid_row) {
                    RoomRate::where([['agent_id', $agent_id], ['season_id', $season_id], ['room_type_id', $roomtypeid_row]])->delete();
                }

                $indexx = 0;
                foreach ($roomtypeid as $roomtypeid_row) {
                    //$Room_type_id=$Room_type_row->room_type_id;
                    foreach ($MealPlan as $MealPlan_row) {
                        $MealPlan_id = $MealPlan_row->id;

                        foreach ($Room_Categories as $Room_Categories_raw) {
                            $room_categories_id = $Room_Categories_raw->room_categories_id;
                            $row['agent_id'] = $agent_id;
                            $row['season_id'] = $season_id;
                            $row['meal_plan_id'] = $MealPlan_id;
                            $row['room_type_id'] = $roomtypeid_row;
                            if ($get_amount[$indexx] == null || $get_amount[$indexx] == "") {
                                $row['rate'] = null;
                            } else {
                                $rate = $get_amount[$indexx];
                                $valuvecy = $Cyrate * $rate;
                                $row['rate'] = $valuvecy;
                            }
                            $row['room_category'] = $room_categories_id;
                            $row['created_by'] = $user->id;
                            $row['updated_by'] = $user->id;
                            $row['status'] = '1';
                            $row['created_at'] = date("Y-m-d h:i:s");
                            $row['updated_at'] = date("Y-m-d h:i:s");
                            $rommrate[] = $row;

                            $indexx++;
                        }
                    }
                }
                //dd($indexx);
                //dd($rommrate);
                RoomRate::insert($rommrate);

                $data = [
                    'status' => '200',
                    'error_status' => '0',
                    'msg' => 'Room rate updated successful'
                ];

                return redirect()->route('agent_room_rate_view')->with('status', $data);
            } catch (QueryException $e) {

                dd($e);

                $data = [
                    'status' => '400',
                    'error_status' => '2',
                    'msg' => 'Room rate add failed'
                ];

                return redirect()->route('add_update_room_rate_view')->with('status', $data);
            } catch (Exception $e) {
                dd($e);
                // if something went wrong

                $data = [
                    'status' => '400',
                    'error_status' => '1',
                    'msg' => 'Room rate update failed'
                ];

                return redirect()->route('add_update_room_rate_view')->with('status', $data);
            }
            // if there is an already added agent rate to the given data then it's duplicate

            $data = [
                'status' => '400',
                'error_status' => '2',
                'msg' => 'duplicate entry'
            ];

            return redirect()->route('add_update_room_rate_view')->with('status', $data);
        } else {

            // if no room rate id then it's a new entry

            try {

                RoomRate::where([['agent_id', $agent_id], ['season_id', $season_id], ['room_type_id', $roomtypeid]])->delete();
                $indexx = 0;

                foreach ($room_type_id_add as $Room_type_id) {
                    //$Room_type_id=$Room_type_row->room_type_id;
                    foreach ($MealPlan as $MealPlan_row) {
                        $MealPlan_id = $MealPlan_row->id;

                        foreach ($Room_Categories as $Room_Categories_raw) {

                            $room_categories_id = $Room_Categories_raw->room_categories_id;
                            $row['agent_id'] = $agent_id;
                            $row['season_id'] = $season_id;
                            $row['meal_plan_id'] = $MealPlan_id;
                            $row['room_type_id'] = $Room_type_id;
                            if ($get_amount[$index] == null || $get_amount[$index] == "") {
                                $row['rate'] = null;
                            } else {
                                $rate = $get_amount[$index];
                                $valuvecy = $Cyrate * $rate;
                                $row['rate'] = $valuvecy;
                            }
                            $row['room_category'] = $room_categories_id;
                            $row['created_by'] = $user->id;
                            $row['updated_by'] = $user->id;
                            $row['status'] = '1';
                            $row['created_at'] = date("Y-m-d h:i:s");

                            $rommrate[] = $row;
                            $index++;
                        }
                    }
                }
                //dd($rommrate);
                RoomRate::insert($rommrate);



                $data = [
                    'status' => '200',
                    'error_status' => '0',
                    'msg' => 'New room rate added successfully'
                ];

                return redirect()->route('agent_room_rate_view')->with('status', $data);
            } catch (QueryException $e) {

                if ($e->getCode() == 23000) {


                    $data = [
                        'status' => '400',
                        'error_status' => '2',
                        'msg' => 'There is an reservation to the given data'
                    ];

                    return redirect()->route('add_update_room_rate_view')->with('status', $data);
                } else {

                    dd($e);
                    $data = [
                        'status' => '400',
                        'error_status' => '2',
                        'msg' => 'Room rate add failed'
                    ];

                    return redirect()->route('add_update_room_rate_view')->with('status', $data);
                }
            } catch (Exception $e) {


                dd($e);
                // if something went wrong

                $data = [
                    'status' => '400',
                    'error_status' => '1',
                    'msg' => 'Room rate add failed'
                ];

                return redirect()->route('add_update_room_rate_view')->with('status', $data);
            }
        }
    }


    public function checkcurrys_rate(Request $req)
    {

        try {

            $params['symble'] = GetSystemUserSymble();

            return response()->JSON($params);
        } catch (Exception $e) {

            $data['status'] = 1;
            $data['msg'] = 'Something went wrong';

            return response()->JSON($data);
        }
    }

    public function deleterate(Request $request)
    {
        $season_id = $request->season_id;
        $agent_id = $request->agent_id;
        
        try {
            
           $get_id= RoomRate::where('season_id', $season_id)->where('agent_id', $agent_id)->get();
           foreach($get_id as $idss)
           {
            RoomRate::where('id', $idss->id)->delete();
           }

        }catch (QueryException $e) {
           
            return response()->json($e);
        }
    }
}
