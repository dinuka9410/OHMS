<?php

namespace Modules\PrimaryModule\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Modules\PrimaryModule\Models\Agent;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use Yajra\DataTables\DataTables;

use function App\FormatDateTime;

class AgentController extends Controller
{
    public function agents_view(Request $req)
    {

        // this is used in the top page navigation
        // ex : dashboard >> settings >> some other page
        // please provide page name and route name and key value pair in the
        // below associative array

        $params['pagenames'] = [
            [
                'displayname' => 'Agents',
                'routename' => 'agents_view'
            ],

        ];

        // this function will return agent view page

        return view('primarymodule::pages/agents_view', $params);
    }


    public function getAgents(Request $req)
    {

        $agents = Agent::orderBy('created_at','DESC')->get();

        return DataTables::of($agents)
            ->addIndexColumn()
            ->addColumn('edit-btn', function ($row) {
                return '<div class="flex justify-center items-center"><a style="margin-top: 0%" class="flex items-center mr-3 text-theme-1" href="add_update_agent_view?id=' . $row->id . '" ><i class="fas fa-edit"></i></a><a style="margin-top: 0%" class="flex items-center mr-3 text-theme-1" href="#" onclick="deleteAgent('.$row->id.')" ><i class="fas fa-trash"></i></a</div>';
            })
            ->addColumn('status', function($row){
                if ($row->status == 1) return '<div class="flex justify-center items-center mt-2"><div class="onoffswitch">
                <input onclick="changeStatus('.$row->id.','.$row->status.')"  type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="room'.$row->id.'" tabindex="0" checked ><label class="onoffswitch-label" for="room'.$row->id.'"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label></div>
                </div>';
                if ($row->status == 0) return '<div class="flex justify-center items-center mt-2"><div  class="onoffswitch">
                <input onclick="changeStatus('.$row->id.','.$row->status.')"  type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="room'.$row->id.'" tabindex="0" ><label class="onoffswitch-label" for="room'.$row->id.'"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label></div>
                </div>';

            })



            ->rawColumns(['edit-btn','status'])
            ->make(true);
    }



    // this function will add or update an agent to the database


    public function add_update_agent_view(Request $req)
    {


        // this is used in the top page navigation
        // ex : dashboard >> settings >> some other page
        // please provide page name and route name and key value pair in the
        // below associative array

        $params['pagenames'] = [

            [
                'displayname' => 'Agent Room Rates',
                'routename' => 'agents_view'
            ],

            [
                'displayname' => 'Add / Edit Agents',
                'routename' => 'add_update_agent_view'
            ],

        ];

        // get all the agents to show in the table

        if (isset($req->id)) {

            try {

                $details = Agent::with(['created_user', 'updated_user'])->where([['id', $req->id]])->first();


                if ($details) {
                    // is there is relvant data then append to the status info
                    $params['details'] = $details;
                    $params['status_info'] = array('status' => $details->status == '1' ? 'Active' : 'Inactive', 'created_by' => $details->created_user->name, 'created_at' => FormatDateTime($details->created_at), 'updated_by' => $details->updated_user->name, 'updated_at' => FormatDateTime($details->updated_at));
                }

                return view('primarymodule::pages/agent_add_update', $params);

            } catch (Exception $e) {


                $data = [
                    'status' => '400',
                    'error_status' => '1',
                    'msg' => 'something went wrong'
                ];

                return redirect('agents_view')->with('status', $data);
            }


            // if there is no req->id then it's a new entry so return a empty form
        } else {

            return view('primarymodule::pages/agent_add_update', $params);
        }
    }



    // this function will actaully add or update the agent to db if valid

    public function agent_add_edit(Request $req)
    {

        // if there is an agent id that means the data should be updated not add a new record

        if (isset($req->agent_id) && $req->agent_id != '') {

            $rules = [

                'agent_name' => ['required', 'string'],
                'agent_email' => ['required', 'email:rfc'],
                'agent_address' => ['required'],
                'agent_contact_person' => ['required'],
                'tel_no_1' => ['required']
            ];

            $msg = [
                'agent_code.unique' => 'please enter a unique Agent code',
                'agent_name.required' => 'please enter valid Agent name',
                'agent_email.required' => 'please enter valid Agent email',
                'agent_contact_person.required' => 'please enter valid Agent contact person name',
                'tel_no_1.required' => 'please enter valid Agent telephone number',
            ];

            $agentdetails = agent::where(['id' => $req->agent_id])->first();


            if ($agentdetails->agentCode != $req->agent_code) {

                $rules += ['agent_code' => ['required','unique:agents,agentCode']];
            }

            $user = Auth::user();

            $validation = Validator::make($req->all(), $rules, $msg)->validate();


            // this means that the data should be upated to the particular id

            try {

                Agent::where([
                    ['id', $req->agent_id]
                ])->update([
                    'agentCode' => $req->agent_code,
                    'agentName' => $req->agent_name,
                    'agentEmail' => $req->agent_email,
                    'agentAddress' => $req->agent_address,
                    'agentContactPerson' => $req->agent_contact_person,
                    'agentRating' => $req->agent_rating,
                    'tel_no_1' => $req->tel_no_1,
                    'tel_no_2' => $req->tel_no_2,
                    'updated_by' => $user->id,
                    'status' => $req->form_status,
                    'updated_at' => date("Y-m-d h:i:s")
                ]);

                $data = [
                    'status' => '200',
                    'error_status' => '0',
                    'msg' => 'Agent updated successfully'
                ];

                return redirect()->route('agents_view')->with('status', $data);
            } catch (QueryException $e) {



                $data = [
                    'status' => '400',
                    'error_status' => '2',
                    'msg' => 'Agent update failed'
                ];

                return redirect('agents_view')->with('status', $data);
            } catch (Exception $e) {

                // if something went wrong

                $data = [
                    'status' => '400',
                    'error_status' => '1',
                    'msg' => 'Agent update failed'
                ];

                return redirect('agents_view')->with('status', $data);
            }
        } else {

            // if  there is no agent id that means it's a new entry so insert a new record to table agents

            $rules = [
                'agent_code' => ['required', 'unique:agents,agentCode'],
                'agent_name' => ['required', 'string'],
                'agent_email' => ['required', 'email:rfc'],
                'agent_address' => ['required'],
                'agent_contact_person' => ['required'],
                'tel_no_1' => ['required']
            ];

            $msg = [
                'agent_code.unique' => 'please enter a unique Agent code',
                'agent_name.required' => 'please enter valid Agent name',
                'agent_email.required' => 'please enter valid Agent email',
                'agent_contact_person.required' => 'please enter valid Agent contact person name',
                'tel_no_1.required' => 'please enter valid Agent telephone number',
            ];


            $user = Auth::user();

            $validation = Validator::make($req->all(), $rules, $msg)->validate();


            try {

                agent::create([
                    'agentCode' => $req->agent_code,
                    'agentName' => $req->agent_name,
                    'agentEmail' => $req->agent_email,
                    'agentAddress' => $req->agent_address,
                    'agentContactPerson' => $req->agent_contact_person,
                    'agentRating' => $req->agent_rating,
                    'tel_no_1' => $req->tel_no_1,
                    'tel_no_2' => $req->tel_no_2,
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                    'status' => 1,
                    'created_at' => date("Y-m-d h:i:s")
                ]);

                $data = [
                    'status' => '200',
                    'error_status' => '0',
                    'msg' => 'New Agent added successfully'
                ];

                return redirect()->route('agents_view')->with('status', $data);

            } catch (QueryException $e) {



                $data = [
                    'status' => '400',
                    'error_status' => '2',
                    'msg' => 'Agent add failed'
                ];

                return redirect('agents_view')->with('status', $data);


            } catch (Exception $e) {

                // if something went wrong

                $data = [
                    'status' => '400',
                    'error_status' => '1',
                    'msg' => 'Agent add failed'
                ];

                return redirect('agents_view')->with('status', $data);
            }
        }


        // end of add update function for agent
    }




    public function get_agent_by_id(Request $req)
    {

        try {

            $data['status'] = 0;
            $data = Agent::where('id', $req->id)->first();

            return response()->JSON($data);
        } catch (Exception $e) {

            $data['status'] = 1;
            $data['msg'] = 'something went wrong';

            return response()->JSON($data);
        }
    }


    public function deleteAgent(Request $req){

        try {

            $agentId = $req->agentId;

            $agent = Agent::where('id','=',$agentId)->first();

            $roomRateExists = $agent->roomRateExists;

            if($roomRateExists){

                $data = ['status'=>500,'error_status'=>1,'msg'=>'unable to delete agent, because agent room rate exists'];

                return response()->json($data);

            }else{

                Agent::where('id','=',$agentId)->delete();

                $data = ['status'=>200,'error_status'=>0,'msg'=>'Agent deleted successfully'];

                return response()->json($data);

            }

        } catch (Exception $e) {

            $data = [
                'status'=>500,
                'error_status'=>1,
                'msg'=>'unable to delete agent',
                'error_msg'=>$e->getMessage(),
            ];

            return response()->json($data);

        }

    }


    public function change_agent_status(Request $req){

        try {

           Agent::where('id','=',$req->agent_id)->update(['status'=>$req->status]);

           $data = [
               'error_status'=>0,
               'msg'=>'Agent status changed',
           ];

           return response()->json($data);

        } catch (Exception $e) {

            $data = [
                'status'=>500,
                'error_status'=>1,
                'msg'=>'unable to change status',
            ];

            return response()->json($data);

        }

    }

    public function validate_agent_code(Request $req){

        $rows = Agent::where('agentCode','=',$req->agent_code)->where('id','!=',$req->agent_id)->exists();

        return response()->json($rows);

    }


    public function validate_agent_name(Request $req){

        $rows = Agent::where('agentName','=',$req->agent_name)->where('id','!=',$req->agent_id)->exists();

        return response()->json($rows);

    }


    public function validate_agent_email(Request $req){

        $rows = Agent::where('agentEmail','=',$req->agent_email)->where('id','!=',$req->agent_id)->exists();

        return response()->json($rows);

    }

}
