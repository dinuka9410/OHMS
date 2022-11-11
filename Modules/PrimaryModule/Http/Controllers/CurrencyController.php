<?php

namespace Modules\PrimaryModule\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Modules\PrimaryModule\Models\Cfg_currency;
use App\Models\User;
use Exception;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class CurrencyController extends Controller
{
    public function dropdown_currency_update(Request $req){

        $user = Auth::user();
  
        try{
  
              // get the rates from the API 
  
            $response = Http::asForm()->get('https://freecurrencyapi.net/api/v2/latest', [
              'apikey'=>'5VkdgKUphGmcoPiNguB7LGusbQSjmSEEbCqffQNi',
              'base_currency'=>'LKR'
            ]);
  
            // turn the result to JSON
  
            $curriences = $response->JSON();
  
  
            // get the selected curency db data from the db
  
            $db_currency = Cfg_currency::where('id','=',$req->currency_id)->first();
  
            // select the rate of the user selected currency from the API response data
            $api_currency_rate = $curriences['data'][$db_currency->c_name];
  
            
            if($db_currency->c_rate!=$api_currency_rate){
  
                // if the db selected currency rate is not equal to api selected currency rate
                // then udpate the database for relvant currency rate
  
                DB::beginTransaction();
  
                try{
  
                  cfg_currency::where('id','=',$db_currency->id)->update(['c_rate'=>$api_currency_rate]);
  
                  User::where('id','=',$user->id)->update(['currency_id'=>$req->currency_id]);
  
                  DB::commit();
  
                  
                  $msg = array('status_code' =>0,'msg'=>'test','rate'=>$api_currency_rate,'symbol'=>$db_currency->c_symbol);
  
                  return response()->JSON($msg);
  
                  // then add the currency rate currency symbol and current currency type to the session
  
                }catch(Exception $e){
                  
                  DB::rollBack();
                  
                  $msg = array('status_code' =>1,'msg'=>'something went wrong','rate'=>$db_currency->c_rate,'symbol'=>$db_currency->c_symbol);
  
                  return response()->JSON($msg);
  
                }
  
            }else{
  
              // then the db is upto date with currency rates so no need to update
  
              // then add the currency rate currency symbol and current currency type to the session
  
                User::where('id','=',$user->id)->update(['currency_id'=>$req->currency_id]);
  
                $msg = array('status_code' =>0,'msg'=>'success no update needed','rate'=>$db_currency->c_rate,'symbol'=>$db_currency->c_symbol);
  
                return response()->JSON($msg);
  
            }
  
  
        }catch(Exception $e){
  
  
          // the database currency rates won't update but the user preffered currency will be updated for 
          // work with the local exchange rates
          
          
  
          User::where('id','=',$user->id)->update(['currency_id'=>$req->currency_id]);
  
          $db_currency = Cfg_currency::where('id','=',$req->currency_id)->first();
  
          $msg = array('status_code' =>1,'msg'=>'something went wrong','rate'=>$db_currency->c_rate,'symbol'=>$db_currency->c_symbol,'error_msg'=>$e->getMessage());
  
          return response()->JSON($msg);
  
        }
  
      }
  
  
      // get all the currencies from the database and return to the front end to add to the
      // dropdown to select the currency type
  
      public function get_currencies(Request $req){
  
        $user = Auth::user();
  
        $final['current'] = $user->currency_id;
  
        $final['currencies'] = cfg_currency::all();
  
        $gate_rate=Cfg_currency::where('id','=',$user->currency_id)->get()->pluck('c_rate')->toarray();
        $gate_symbol=Cfg_currency::where('id','=',$user->currency_id)->get()->pluck('c_symbol')->toarray();
  
        Session::put('currency_symbol', $gate_symbol);
        Session::put('currency_rate', $gate_rate);
       

        $final['testing'] = Session::get('currency_symbol');
      
        return response()->json($final);
       
  
  
      }
  
  
  
      // this function will return the rate of the user selected currency to the currency converstion function
  
      public function get_currency_rate(Request $req){
  
        $data =  cfg_currency::where('id','=',$req->new_currency)->first();
  
        return response()->json($data);
  
      }
}
