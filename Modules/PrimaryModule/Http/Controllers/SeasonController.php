<?php

namespace Modules\PrimaryModule\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\PrimaryModule\Models\Season;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Database\QueryException;

use Illuminate\Routing\Controller;
use Modules\PrimaryModule\Models\RoomBookingReservation;
use Modules\PrimaryModule\Models\RoomRate;
use Modules\PrimaryModule\Models\RoomReservation;
use Yajra\DataTables\DataTables;

class SeasonController extends Controller
{


    public function add_update_season_view(Request $req)
    {

        // this is used in the top page navigation
        // ex : dashboard >> settings >> some other page
        // please provide page name and route name and key value pair in the
        // below associative array

        $params['pagenames'] = [
            [
                'displayname' => 'Add / Edit Season',
                'routename' => 'add_update_season_view'
            ],

        ];


        // send all the seasons to show in the table
        $all_seasons = Season::where([['status', '1']])->get();

        $params['seasons'] = $all_seasons;

        return view('primarymodule::pages/season_add_update', $params);
    }


    public function getAllSeasons(Request $req)
    {

        $seasons = Season::orderBy('created_at', "DESC")->get();

        return DataTables::of($seasons)->make(true);
    }


    public function get_season_by_id(Request $req)
    {

        try {


            $season = Season::where('id', $req->id)->first();

            $bookingsExists = $season->bookingsExists;
            $reservationsExists = $season->reservationsExists;

            if ($bookingsExists || $reservationsExists) {

                // wont't allow to edit season dates because there are reservations or booking that can
                // conflict

                $canEditDate = false;
            } else {

                $canEditDate = true;
            }

            $data = [
                'status' => 0,
                'error_status' => 0,
                'msg' => 'successfully fetched',
                'data' => $season,
                'canEditDate' => $canEditDate,
            ];

            return response()->JSON($data);
        } catch (Exception $e) {

            $data = [
                'status' => 1,
                'error_status' => 1,
                'msg' => 'unable to fetch data',
                'error_message' => $e->getMessage(),
            ];

            return response()->JSON($data);
        }
    }

    // this function will actaully add or update the season to db if valid

    public function season_add_edit(Request $req)
    {

        // first check if the season id is available, if id is present then uupdate rules will be defiened

        if (isset($req->season_id) && $req->season_id != '') {

            $rules = [
                's_name' => ['required', 'string'],
                's_end_date' => ['required', 'date'],
            ];

            $msg = [
                's_code.required' => 'please enter a valid season code',
                's_code.unique' => 'please provide an unqiue season code',
                's_name.required' => 'please enter valid season name',
                's_start_date.required' => 'the starting date is already defined',
                's_start_date.unique' => 'the starting date is already defined',
                's_end_date.required' => 'please enter valid season end date',
            ];


            // check whether there is a id which means update proceeder should be carried out

            if (isset($req->season_id) && $req->season_id != "") {

                $seasondetails = Season::where(['id' => $req->season_id])->first();

                if ($seasondetails->seasonCode != $req->s_code) {
                    $rules += ['s_code' => ['required', 'unique:seasons,seasonCode']];
                }

                if ($seasondetails->start_date != $req->s_start_date) {
                    $rules += ['s_start_date' => ['required', 'unique:seasons,start_date']];
                }
            }


            $user = Auth::user();

            $validation = Validator::make($req->all(), $rules, $msg)->validate();


            // this means that the data should be upated to the particular id

            try {

                Season::where([
                    ['id', $req->season_id]
                ])->update([
                    'seasonCode' => $req->s_code,
                    'seasonName' => $req->s_name,
                    'start_date' => $req->s_start_date,
                    'end_date' => $req->s_end_date,
                    'updated_by' => $user->id,
                    'updated_at' => date("Y-m-d h:i:s")
                ]);

                $data = [
                    'status' => '200',
                    'error_status' => '0',
                    'msg' => 'season updated successfully'
                ];

                return redirect()->route('add_update_season_view')->with('status', $data);
                //return back()->with('status',$data);

            } catch (QueryException $e) {


                $data = [
                    'status' => '400',
                    'error_status' => '2',
                    'msg' => 'season update failed'
                ];

                return redirect('add_update_season_view')->with('status', $data);
            } catch (Exception $e) {

                // if something went wrong

                $data = [
                    'status' => '400',
                    'error_status' => '1',
                    'msg' => 'season update failed'
                ];

                return redirect('add_update_season_view')->with('status', $data);
            }


            // if there is no id that means a new season entry

        } else {

            // define  rules for new entry

            $rules = [
                's_name' => ['required', 'string'],
                's_code' => ['required', 'unique:seasons,seasonCode'],
                's_start_date' => ['required', 'unique:seasons,start_date'],
                's_end_date' => ['required', 'date'],
            ];

            $msg = [
                's_code.required' => 'please enter a valid season code',
                's_code.unique' => 'please provide an unqiue season code',
                's_name.required' => 'please enter valid season name',
                's_start_date.required' => 'the starting date is already defined',
                's_start_date.unique' => 'the starting date is already defined',
                's_end_date.required' => 'please enter valid season end date',
            ];

            $user = Auth::user();

            $validation = Validator::make($req->all(), $rules, $msg)->validate();

            // if  there is no season id that means it's a new entry so insert a new record to table seasons

            try {

                Season::create([
                    'seasonCode' => $req->s_code,
                    'seasonName' => $req->s_name,
                    'start_date' => $req->s_start_date,
                    'end_date' => $req->s_end_date,
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                    'created_at' => date("Y-m-d h:i:s"),
                    'status' => 1
                ]);

                $data = [
                    'status' => '200',
                    'error_status' => '0',
                    'msg' => 'New season added successfully'
                ];

                return redirect()->route('add_update_season_view')->with('status', $data);
                // return redirect('add_update_season_view')->with('status',$data);


            } catch (QueryException $e) {


                $data = [
                    'status' => '400',
                    'error_status' => '2',
                    'msg' => 'Season add failed'
                ];

                return redirect('add_update_season_view')->with('status', $data);
            } catch (Exception $e) {


                // if something went wrong

                $data = [
                    'status' => '400',
                    'error_status' => '1',
                    'msg' => 'season add failed'
                ];

                return redirect('add_update_season_view')->with('status', $data);
            }
        }


        // end of add update function for season
    }




    public function delete_season(Request $req)
    {

        $id = $req->season_id;

        try {

            $season = season::where('id', '=', $id)->first();
            $roomRatesExists = $season->roomRateExists;

            if ($roomRatesExists) {

                $data = [
                    'status' => '500',
                    'error_status' => '1',
                    'msg' => 'Agent room rates exist for this season'
                ];

                return response()->json($data);
            } else {

                Season::where([['id', $id]])->delete();

                $data = [
                    'status' => '200',
                    'error_status' => '0',
                    'msg' => 'Season deleted successfully'
                ];

                return response()->json($data);
            }
        } catch (Exception $e) {

            $data = [
                'status' => '400',
                'error_status' => '1',
                'msg' => 'season delete failed',
                'error_msg' => $e->getMessage(),
            ];

            return response()->json($data);
        }
    }


    public function change_season_status(Request $req)
    {

        try {

            Season::where('id', '=', $req->id)->update(['status' => $req->status]);

            $data = [
                'error_status' => 0,
                'msg' => 'season status changed',
            ];

            return response()->json($data);
        } catch (Exception $e) {

            $data = [
                'status' => 500,
                'error_status' => 1,
                'msg' => 'unable to change status',
            ];

            return response()->json($data);
        }
    }


    public function validate_season_code(Request $req)
    {

        $rows = Season::where('seasonCode', '=', $req->s_code)->where('id', '!=', $req->season_id)->exists();

        return response()->json($rows);
    }

    public function validate_season_name(Request $req)
    {

        $rows = Season::where('seasonName', '=', $req->s_name)->where('id', '!=', $req->season_id)->exists();

        return response()->json($rows);
    }
}
