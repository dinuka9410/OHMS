<?php

namespace Modules\PrimaryModule\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;

use Exception;

use Illuminate\Support\Facades\Auth;

use \Illuminate\Database\QueryException;


class PageController extends Controller
{
    public function show_settings(Request $req){

        // this is used in the top page navigation
        // ex : dashboard >> settings >> some other page
        // please provide page name and route name and key value pair in the
        // below associative array

        $params['pagenames'] = [
            [
                'displayname'=>'settings',
                'routename'=>'settings'
            ],

        ];


        return view('primarymodule::pages/settings-page',$params);

    }

    public function change_layout_theme(Request $req){

        $valiadation = $req->validate([
            'layout_name'=>'required',
        ]);

        try{


            if($req->theme_color=="on"){
                $color = "dark";
            }else{
                $color = "light";
            }

            $user = Auth::user();

            user::where([

                ['id',$user->id]

            ])->update([
                'layout'=>$req->layout_name,
                'theme'=>$color,
            ]);

            session()->put('layout',$req->layout_name);
            session()->put('theme',$color);

            $data = [
                'status'=>'200',
                'error_status'=>'0',
                'msg'=>'Theme setting updated successfully'
            ];

            return back()->with('status',$data);

        }catch(QueryException $ex){


            $data = [
                'status'=>'400',
                'error_status'=>'2',
                'msg'=>'theme settings update failed'
            ];

            return back()->with('status',$data);

        }catch(Exception $e){


            $data = [
                'status'=>'400',
                'error_status'=>'1',
                'msg'=>'theme settings update failed'
            ];


           return back()->with('status',$data);

        }

    }
}
