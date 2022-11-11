<?php

namespace Modules\PrimaryModule\Http\Controllers;

use App\Models\Notifications;
use Exception;
use Illuminate\Http\Request;

use Illuminate\Routing\Controller;
use Modules\PrimaryModule\Models\Cfg_module;
use Modules\PrimaryModule\Models\UserGroup;
use Modules\PrimaryModule\Models\UserGroupPermission;
use Modules\PrimaryModule\Models\UserPermission;
use Illuminate\Support\Facades\DB;
use app\Models\User;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Modules\PrimaryModule\Models\Cfg_branch;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;

class UserController extends Controller
{

    public function userView(Request $req)
    {

        $params['pagenames'] = [

            [
                'displayname' => 'users',
                'routename' => 'users_view'
            ],

        ];

        try {

            return view('primarymodule::pages/users_view', $params);
        } catch (Exception $e) {

            return redirect()->route('dashboard');
        }
    }


    // this function call by frontend data table to populate via jquery

    public function getAllUsers()
    {

        $users = User::join('user_groups', 'users.user_group_id', '=', 'user_groups.user_group_id')
            ->join('cfg_branches', 'cfg_branches.id', '=', 'users.branch_id')
            ->select('name', 'gender', 'user_group_name', 'b_name', 'users.id as user_id', 'users.status', 'users.created_at')->orderBy('created_at', 'DESC')->get();

        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('edit-btn', function ($row) {
                return '<div class="flex justify-center items-center mt-2"><a  href="user_add_edit_view?user_id=' . $row->user_id . '" class=" text-white" ><i class="fa fa-edit" aria-hidden="true"></i></a></div>';
            })
            ->addColumn('status', function ($row) {
                if ($row->status == 1) return '<div class="flex justify-center items-center mt-2"><div class="onoffswitch">
            <input onclick="changeStatus(' . $row->user_id . ',' . $row->status . ')"  type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="room' . $row->user_id . '" tabindex="0" checked ><label class="onoffswitch-label" for="room' . $row->user_id . '"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label></div>
            </div>';
                if ($row->status == 0) return '<div class="flex justify-center items-center mt-2"><div  class="onoffswitch">
            <input onclick="changeStatus(' . $row->user_id . ',' . $row->status . ')"  type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="room' . $row->user_id . '" tabindex="0" ><label class="onoffswitch-label" for="room' . $row->user_id . '"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label></div>
            </div>';
            })
            ->rawColumns(['edit-btn', 'status'])->make(true);
    }


    // this function will return the add or update user view

    public function userAddEdit(Request $req)
    {

        $params['pagenames'] = [

            [
                'displayname' => 'users',
                'routename' => 'users_view'
            ],

            [
                'displayname' => 'User Add/Edit',
                'routename' => 'user_add_edit_view'
            ],

        ];

        try {


            $params['user_groups'] = UserGroup::all();
            $params['branches'] = Cfg_branch::all();

            $permission = false;

            $loggedInUser = Auth::user();

            $permission = UserGroupPermission::where(
                [
                    'user_group_id' => $loggedInUser->user_group_id,
                    'permission_code' => 31
                ]
            )->exists();

            // first check if logged in user is administrtor then by default set access to true,

            if ($loggedInUser->user_group_id == 1) {
                $permission = true;
            }

            if ($req->user_id) {

                $user = User::join('user_groups', 'users.user_group_id', '=', 'user_groups.user_group_id')->join('cfg_branches', 'cfg_branches.id', '=', 'users.branch_id')->where('users.id', $req->user_id)->first();

                // this has permissions means to change the user group, if the current user has no permissions to view the add edit page he can't update his user group and branch but can update other details by clicking on profile icon on top menu.

                if (!$permission) {

                    if ($loggedInUser->id == $req->user_id) {

                        // can access the page but change user group and branch is disabled
                        $permission = false;
                    } else {


                        $data = [
                            'status' => 401,
                            'error_status' => 1,
                            'msg' => 'Unauthorized access',
                        ];

                        return Redirect()->route('dashboard')->with('status', $data);
                    }
                }

                $params['permission'] = $permission;
                $params['user'] = $user;
                $params['user_id'] = $req->user_id;

                return view('primarymodule::pages/user_add_edit_view', $params);
            } else {

                if ($permission) {

                    $params['permission'] = $permission;
                    return view('primarymodule::pages/user_add_edit_view', $params);
                } else {

                    $data = [
                        'status' => 401,
                        'error_status' => 1,
                        'msg' => 'Unauthorized access',
                    ];

                    return Redirect()->route('dashboard')->with('status', $data);
                }
            }
        } catch (Exception $e) {
            // dd($e);
            $data = [
                'status' => '500',
                'error_status' => '1',
                'msg' => 'something went wrong',
            ];

            return redirect()->route('dashboard')->with('status', $data);
        }
    }



    public function addUpdateUser(Request $req)
    {

        $rules = [
            'username' => ['required', Rule::unique('users', 'username')->ignore($req->user_id)],
            'user_name' => 'required',
            'user_email' => ['required', Rule::unique('users', 'email')->ignore($req->user_id)],
            'user_gender' => 'required',
            'user_branch' => 'required',
            'user_group' => 'required',
            'user_contact' => 'required',
            'user_address' => 'required',
            'user_password' => 'required',
            'confirm_password' => 'required|same:user_password',
        ];


        $validation = Validator::make($req->all(), $rules)->validate();


        try {


            $obj = [
                'username' => $req->username,
                'name' => $req->user_name,
                'email' => $req->user_email,
                'layout' => 'side-menu',
                'theme' => 'light',
                'currency_id' => 1,
                'gender' => $req->user_gender,
                'user_group_id' => $req->user_group,
                'contact_no' => $req->user_contact,
                'address' => $req->user_address,
                'status' => 1,
                'branch_id' => $req->user_branch,
            ];


            if ($req->user_password != "edited") {

                $obj['password'] = Hash::make($req->user_password);
            }

            //$user = User::UpdateorCreate(['name'=>$req->user_name],$obj);

            if ($req->user_id) {

                User::where('id', '=', $req->user_id)->update($obj);

                $user = User::where('id', '=', $req->user_id)->first();
            } else {

                $user = User::create($obj);
            }


            if ($req->file('room_img') != null) {

                $image = $req->file('room_img');
                $image_name = $user->id . ".jpg";

                $filepath = public_path('storage/img/userprofiles/' . $image_name);

                if (!File::exists($filepath)) {

                    $image->move(public_path('/storage/img/userprofiles'), $image_name);
                } else {

                    File::delete($filepath);
                    $image->move(public_path('/storage/img/userprofiles'), $image_name);
                }
            } else {

                $image_name = null;
            }


            if ($image_name != null) {

                User::where('id', '=', $user->id)->update(['photo' => $image_name]);
            }


            $data = [
                'status' => '200',
                'error_status' => '0',
                'msg' => 'User add/edit successfully completed',
            ];

            return redirect()->route('users_view')->with('status', $data);
        } catch (Exception $e) {

            $data = [
                'status' => '500',
                'error_status' => '1',
                'msg' => 'User add/edit not completed. Please try again.',
            ];

            return redirect()->route('users_view')->with('status', $data);
        }
    }

    public function user_permissions_view(Request $req)
    {

        $params['pagenames'] = [
            [
                'displayname'=>'User Permissions',
                'routename'=>'user_permissions_view'
            ],

        ];

        try {

            $params['user_groups'] = UserGroup::all();

            return view('primarymodule::pages/user_permissions_view', $params);
        } catch (Exception $e) {
            $data = [
                'status' => '400',
                'error_status' => '2',
                'msg' => 'unable to view user permissions'
            ];
            return redirect()->route('dashboard', $data);
        }
    }


    // this function will add or update user group

    public function add_update_user_group(Request $req)
    {

        try {

            UserGroup::create([
                'user_group_name' => $req->u_group_name,
            ]);

            $data = [
                'status' => 200,
                'error_status' => 0,
                'msg' => 'user group added successfully'
            ];

            return response()->json($data);
        } catch (Exception $e) {

            $data = [
                'status' => 500,
                'error_status' => 1,
                'msg' => $e->getMessage(),
            ];


            return response()->json($data);
        }
    }


    // this function will get the user permissions according to the given user group

    public function getUserGroupPermissions(Request $req)
    {

        $user_group = $req->user_group;

        try {

            $result = Cfg_module::with(['relatedPermissions.UserGroupPermissions' => function ($q) use ($user_group) {

                $q->where('user_group_id', $user_group);
            }])->where('status', '1')->get();

            $data = [
                'status' => 200,
                'error_status' => 0,
                'msg' => 'fetched permissions successfullly',
                'data' => $result,
            ];

            return response()->json($data);
        } catch (Exception $e) {

            $data = [
                'status' => 500,
                'error_status' => 1,
                'msg' => 'unable to get the relavant permissions list',
                'error' => $e->getMessage(),
            ];

            return response()->json($data);
        }
    }

    // this function will send an request to server to enable or disable user permissions to a particular
    // user group

    public function enableDisablePermissions(Request $req)
    {

        $user_group_id = 0;

        try {

            DB::beginTransaction();

            if ($req->user_group_id == "new") {

                $user_group = UserGroup::create(['user_group_name' => $req->user_group_name]);

                $user_group_id = $user_group->id;
            } else {

                $user_group_id = $req->user_group_id;
            }


            UserGroupPermission::where('user_group_id', '=', $user_group_id)->delete();

            $permissionslist = [];

            if (isset($req->permissions) && count($req->permissions) > 0) {

                foreach ($req->permissions as $permission) {

                    $row['user_group_id'] = $user_group_id;
                    $row['permission_code'] = $permission;

                    $permissionslist[] = $row;
                }
            }

            UserGroupPermission::insert($permissionslist);

            DB::commit();

            $data = [
                'error_status' => '0',
                'msg' => 'Permissions changed successfully',
            ];

            return redirect()->route('user_permissions_view')->with('status', $data);
        } catch (Exception $e) {

            DB::rollBack();

            dd($e);

            $data = [
                'error_status' => 1,
                'msg' => 'unable to assign permissions to user group'
            ];

            return redirect()->route('user_permissions_view')->with('status', $data);
        }
    }


    // this function will bulk enable or disable user group permissions

    public function bulkPermissionEnableDisable(Request $req)
    {

        try {


            if ($req->status == "true") {

                DB::beginTransaction();

                UserGroupPermission::join('user_permissions', 'user_permissions.permission_code', '=', 'user_group_permissions.permission_code')
                    ->where('user_permissions.module_id', '=', $req->module_id)
                    ->where('user_group_permissions.user_group_id', $req->user_group_id)
                    ->delete();

                $permissions = UserPermission::where('module_id', $req->module_id)->get();

                $dbrows = [];

                foreach ($permissions as $permission) {

                    $row['user_group_id'] = $req->user_group_id;
                    $row['permission_code'] = $permission->permission_code;

                    $dbrows[] = $row;
                }


                UserGroupPermission::insert($dbrows);

                DB::commit();

                $data = [
                    'status' => $req->status,
                    'error_status' => 0,
                    'msg' => 'permissions enabled successfully',
                ];

                return response()->json($data);
            } else {

                DB::beginTransaction();

                UserGroupPermission::join('user_permissions', 'user_permissions.permission_code', '=', 'user_group_permissions.permission_code')
                    ->where('user_permissions.module_id', '=', $req->module_id)
                    ->where('user_group_permissions.user_group_id', $req->user_group_id)
                    ->delete();


                DB::commit();

                $data = [
                    'status' => $req->status,
                    'error_status' => 0,
                    'msg' => 'permissions disabled successfully',
                ];

                return response()->json($data);
            }
        } catch (Exception $e) {

            DB::rollback();

            $data = [
                'status' => 500,
                'error_status' => 1,
                'msg' => 'unable to change permission status',
                'error_status' => $e->getMessage(),
            ];


            return response()->json($e);
        }
    }


    public function changeUserStatus(Request $req)
    {

        try {

            User::where('id', '=', $req->user_id)->update(['status' => $req->status]);

            $data = [
                'status' => 200,
                'error_status' => 0,
                'msg' => 'Status updated successfully',
            ];

            return response()->json($data);
        } catch (Exception $e) {

            $data = [
                'status' => 500,
                'error_status' => 1,
                'msg' => 'Unable to change user status',
                'error_msg' => $e->getMessage(),
            ];

            return response()->json($data);
        }
    }


    public function user_validate_user_name(Request $req)
    {

        $rows = User::where('name', '=', $req->user_name)->where('id', '!=', $req->user_id)->exists();

        return response()->json($rows);
    }


    public function user_validate_username(Request $req)
    {

        $rows = User::where('username', '=', $req->username)->where('id', '!=', $req->user_id)->exists();

        return response()->json($rows);
    }

    public function user_validate_email(Request $req)
    {

        $rows = User::where('email', '=', $req->user_email)->where('id', '!=', $req->user_id)->exists();

        return response()->json($rows);
    }
    public function clear_notification(Request $req)
    {
        try {
            Notifications::where('notifiable_id',Auth::user()->id)->where('notifiable_type','App\Models\User')->delete();
            return response()->noContent();
        } catch (Exception $e) {
            return response()->noContent();

        }
    }
}
