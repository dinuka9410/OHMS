<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\PrimaryModule\Models\UserGroupPermission;

class permissionCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public


    function handle(Request $request, Closure $next, $permission_code)
    {
        // this middleware will check whether the user can permission to access the route he/she requesting and
        // if not return back with error msg


        $permission = false;

        try {

            $user = Auth::user();

            $user_group_id = $user->user_group_id;

            $permission = UserGroupPermission::where([
                'user_group_id' => $user_group_id,
                'permission_code' => $permission_code,
            ])->exists();

            if ($permission || $user->id == 1) {
                return $next($request);
            } else {
                $data = [
                    'status' => '400',
                    'error_status' => '3',
                    'msg' => "You don't have access to this. Please contact System Administrator"
                ];
                return redirect()->route('dashboard')->with('status', $data);
            }
        } catch (Exception $e) {
            $data = [
                'status' => '400',
                'error_status' => '3',
                'msg' => 'Unable to direct this page. Please contact System Administrator'
            ];
            return redirect()->route('dashboard')->with('status', $data);
        }
    }
}
