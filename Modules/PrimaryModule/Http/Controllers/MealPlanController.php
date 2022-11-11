<?php

namespace Modules\PrimaryModule\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Modules\PrimaryModule\Models\MealPlan;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Modules\PrimaryModule\Models\RoomRate;

class MealPlanController extends Controller
{
    

 public function add_update_meal_view(Request $req){


     $params['pagenames'] = [

         [
             'displayname'=>'Add / Update Meal Plan',
             'routename'=>'add_update_meal_view'
         ],

     ];


     // send all the seasons to show in the table 
     $all_meals = MealPlan::where([['status','1']])->orderBy('created_at',"DESC")->get();
     $params['meals'] = $all_meals;

     
     if(isset($req->id)){

         try{

            $details = MealPlan::with(['created_user','updated_user'])->where([['meal_plans.id',$req->id]])->first();


            if($details){
                // is there is relvant data then append to the status info
                $params['details'] = $details;
                $params['status_info'] = array('created_by' =>$details->created_user->name,'created_at'=>$details->created_at,'updated_by'=>$details->updated_user->name,'updated_at'=>$details->updated_at);
           
            }

            return view('primarymodule::pages/mealplan_add_update',$params);

         }catch(Exception $e){

            
            $data = [
                'status'=>'400',
                'error_status'=>'1',
                'msg'=>'something went wrong'
            ];

            return redirect('add_update_meal_view')->with('status',$data);

         }


         
     // if there is no req->id then it's a new entry so return a empty form

        }else{

         return view('primarymodule::pages/mealplan_add_update',$params);

        }


 }



 public function meal_add_edit(Request $req){

      // first check if the meal id is available, if id is present then uupdate rules will be defiened

      if(isset($req->meal_id)&&$req->meal_id!=''){

         $rules = [
             'meal_name'=>['required','string'],
         ];
 
         $msg = [
             'meal_name.required'=>'please insert a valid meal plan name',
             'meal_code.unique'=>'please provide an unqiue meal plan code',
             'meal_code.required'=>'please enter a valid meal plan code'
         ];
         

         // check whether there is a id which means update proceeder should be carried out

         if(isset($req->meal_id)&&$req->meal_id!=""){

             $mealdetails = MealPlan::where(['id'=>$req->meal_id])->first();

             if($mealdetails->mealPlanCode!=$req->meal_code){
                 $rules += ['meal_code'=>['required','unique:meal_plans,mealPlanCode']];
             }
 
         }


         $user = Auth::user();
 
         $validation = Validator::make($req->all(),$rules,$msg)->validate();

 
         // this means that the data should be upated to the particular id
 
                 try{
 
                     MealPlan::where([
                         ['id',$req->meal_id]
                     ])->update([
                         'mealPlanCode'=>$req->meal_code,
                         'mealPlanName'=>$req->meal_name,
                         'updated_by'=>$user->id,
                         'updated_at'=>date("Y-m-d h:i:s")
                     ]);

                     $data = [
                         'status'=>'200',
                         'error_status'=>'0',
                         'msg'=>'meal plan updated successfully'
                     ];
             
                     return redirect()->route('add_update_meal_view')->with('status',$data);

 
                 }catch(QueryException $e){
 
                

                     $data = [
                         'status'=>'400',
                         'error_status'=>'2',
                         'msg'=>'meal plan update failed'
                     ];

                     return redirect('add_update_meal_view')->with('status',$data);
 
                 }catch(Exception $e){
                     
                     // if something went wrong

             
                     $data = [
                         'status'=>'400',
                         'error_status'=>'1',
                         'msg'=>'meal plan update failed'
                     ];
 
                     return redirect('add_update_meal_view')->with('status',$data);
 
                 }
 
 
     // if there is no id that means a new season entry

        }else{


         $rules = [
             'meal_code'=>['required','unique:meal_plans,mealPlanCode'],
             'meal_name'=>['required','string'],
         ];
 
         $msg = [
             'meal_name.required'=>'please insert a valid meal plan name',
             'meal_code.unique'=>'please provide an unqiue meal plan code',
             'meal_code.required'=>'please enter a valid meal plan code'
         ];
         

         $user = Auth::user();
 
         $validation = Validator::make($req->all(),$rules,$msg)->validate();
 
         // if  there is no season id that means it's a new entry so insert a new record to table seasons
 
                 try{
 
                     MealPlan::create([
                                    'mealPlanCode'=>$req->meal_code,
                         'mealPlanName'=>$req->meal_name,
                         'created_by'=>$user->id,
                         'updated_by'=>$user->id,
                         'created_at'=>date("Y-m-d h:i:s"),
                         'status'=>1
                     ]);

                
                     $data = [
                         'status'=>'200',
                         'error_status'=>'0',
                         'msg'=>'meal plan updated successfully'
                     ];
             
                     return redirect()->route('add_update_meal_view')->with('status',$data);
 
 
                 }catch(QueryException $e){
 
            

                     $data = [
                         'status'=>'400',
                         'error_status'=>'2',
                         'msg'=>'meal plan update failed'
                     ];

                     return redirect('add_update_meal_view')->with('status',$data);
 
                 }catch(Exception $e){
                     
               
                     
                     // if something went wrong
 
                     $data = [
                         'status'=>'400',
                         'error_status'=>'1',
                         'msg'=>'meal plan update failed'
                     ];

                     return redirect('add_update_meal_view')->with('status',$data);
 
                 }


        }


 }

 public function deleteMealPlan(Request $req){

    try {
    
        $meal_plan_id = 1;
        $roomrateExists = RoomRate::where('meal_plan_id','=',$meal_plan_id)->exists(); 
        
        if($roomrateExists){
            
            $data = [
                'status'=>500,
                'error_status'=>1,
                'msg'=>'unable to delete this meal plan, room rates are assigned already',
            ];

            return response()->json($data);

        }else{

            MealPlan::where('id','=',$meal_plan_id)->delete();

            $data = [
                'status'=>200,
                'error_status'=>0,
                'msg'=>'meal plan deleted successfully',
            ];

            return response()->json($data);

        }

    } catch (Exception $e) {
        
        $data = [
            'status'=>500,
            'error_status'=>1,
            'msg'=>'unable to delete meal plan',
        ];

        return response()->json($data);

    }   

 }
 
}
