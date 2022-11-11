<?php

namespace Modules\PrimaryModule\Repositories;

use Modules\PrimaryModule\Repositories\Interfaces\CalculatorInterface;

use Exception;

use DateTime;

use Modules\PrimaryModule\Models\RoomReservation;
use Modules\PrimaryModule\Models\Room;
use Modules\PrimaryModule\Models\Additional_bill;
use Modules\PrimaryModule\Models\Cfg_bill_charges;
use Modules\PrimaryModule\Models\Roomallocation;
use Illuminate\Support\Facades\Auth;
use Modules\PrimaryModule\Models\Cfg_generate_id;
use Modules\PrimaryModule\Models\Cfg_branch;
use Illuminate\Support\Facades\DB;

class CalculatorRepository implements CalculatorInterface
{


//------------------------------------------------------------------------------------------------------------

    public static function generateID($keyword,$table_name,$search_column,$date_param){

        $user = Auth::user();

        $branch = Cfg_branch::where('id','=',$user->branch_id)->select('b_name')->first();

        $todaydate = date_parse(date("Y-m-d"));

        // first check if the ID_month is activite in the config_main table which means
        // the Generated ID's should include the month or else the year only and also if the ID_Month
        // is acitive the ID will reset on each month or if deactivated the ID will geneate and reset yearly

        $settings = Cfg_generate_id::first();

        if($settings->id_month==1){


            // this means ID should be generated with the Month included ad should be
            // reset when new month appears

        // if the table is not empty then fetch the last created_at and compare whether it's a
        // new month or not ,, if there is not records in the table then create a new ID
        //with month included

            $date_param = date_parse($date_param);

            $tempcode = $keyword."/".$branch->b_name."/".substr($date_param['year'], -2).'/'.$date_param['month'].'/';

            $table = DB::table($table_name)->where($search_column,'LIKE',"{$tempcode}%")->latest()->first();
            


                if($table){

                    $record = date_parse($table->created_at);

            // this will check if the previous Generated ID is greater than the current month
            // which means it's a new month then start the ID with 0001 or continue with thelast ID
            // and also this will check if the last ID is null which means that the new ID should be created


                    if($todaydate['month']>$record['month']){

                        // this a new month so get the month no  and  beofore anything else check
                        // whether the $record['year'] is smaller than current year
                        // which means it's a new year then reset the index to 0001

                        // Eg : 2022/01/03 > 2021/12/03

                        if($date_param['year']>$record['year']){

                            // it's a new year because of 2022/01/03 > 2021/12/30

                            $ID = explode("/",$table->$search_column);
                            $yearindex = 2;
                            $monthindex = 3;

                            $idindex = count($ID) - 1;

                            $ID[$yearindex] = substr($date_param['year'], -2);
                            $ID[$monthindex] = $date_param['month'];

                            $ID[$idindex] = sprintf("%04d",1);

                            $new_generated_id = implode('/',$ID);

                            return $new_generated_id;

                        }else{


                        //it's not a new year it's a same year but new month

                            $ID = explode("/",$table->code);

                            $monthindex = 3;

                            $idindex = count($ID) - 1;

                            $ID[$monthindex] = $date_param['month'];

                            $ID[$idindex] = sprintf("%04d",1);

                            $new_generated_id = implode('/',$ID);

                            return $new_generated_id;

                        }


                    }else{

                        // this a same month ID generation, so increase the ID by 1

                        $ID = explode("/",$table->code);

                        $lastindex = count($ID) - 1;

                        $newindex = sprintf("%04d",$ID[$lastindex]+1); // output = 0001 EX

                        $ID[$lastindex] = $newindex;

                        $new_generated_id = implode("/",$ID);

                        return $new_generated_id;

                    }


                }else{

                    // create a new ID with month included there is no records in the table yet

                    $final = $keyword."/".$branch->b_name."/".substr($date_param['year'], -2)."/".$date_param['month'].'/'.sprintf("%04d",1);

                    return $final;

                }

        }else{

            $date_param = date_parse($date_param);

            $tempcode = $keyword."/".$branch->b_name."/".substr($date_param['year'], -2).'/';

            $table = DB::table($table_name)->where($search_column,'LIKE',"{$tempcode}%")->latest()->first();

            // this will only add the year to the ID and genearte ID, the ID will be
            // reset to the new year

            // if the table is not empty then fetch the last created_at and compare whether it's a
        // new year or not ,, if there is not records in the table then create a new ID
        //with year included

            if($table){

                $record = date_parse($table->created_at);

                if($todaydate['year']>$record['year']){

                    $ID = explode("/",$table->code);

                    $yearindex = 2;

                    $idindex = count($ID) - 1;

                    $ID[$yearindex] = substr($date_param['year'], -2);

                    $ID[$idindex] = sprintf("%04d",1);

                    $new_generated_id = implode('/',$ID);

                    return $new_generated_id;

                }else{


                    // this means the same year ID geneartion

                    $ID = explode("/",$table->code);

                    $lastindex = count($ID) - 1;

                    $newindex = sprintf("%04d",$ID[$lastindex]+1); // output = 0001 EX

                    $ID[$lastindex] = $newindex;

                    $new_generated_id = implode("/",$ID);

                    return $new_generated_id;

                }


            }else{

                // new record with year and 0001 id

                $final = $keyword."/".$branch->b_name."/".substr($date_param['year'], -2).'/'.sprintf("%04d",1);

                return $final;

            }


        }

    }

//------------------------------------------------------------------------------------------------------------

    public function no_of_nights($date1,$date2){

        // this function is used to calculate the no of nights between checkin and checkout date

        $checkindate = new DateTime($date1);

        $checkoutdate = new DateTime($date2);

        $interval = $checkindate->diff($checkoutdate);

        return $interval->d;

    }


//-----------------------------------------------------------------------------------------------------------------

    public function get_reservation_total_bill($res_id,$discount){

        try{

            $final = [];

            $res_details = roomReservation::where('id','=',$res_id)->with(['get_travel_agent','get_guest'])->first();
    
            $res_details->nights = $this->no_of_nights($res_details->checkinDate,$res_details->checkoutDate);
            
            $rooms = Room::join('roomallocations','rooms.room_id','=','roomallocations.roomNumber')
            ->join('room_reservations','roomallocations.res_Id','=','room_reservations.id')
            ->join('meal_plans','roomallocations.basis','meal_plans.id')
            ->where('room_reservations.id',$res_id)
            ->select('rooms.*','meal_plans.mealPlanCode','roomallocations.rate')
            ->distinct('rooms.room_name')
            ->with(['get_room_type','get_category','get_agent_rates'])
            ->get();
    
            $room_sub_total = 0;
            $index = 0;
            foreach($rooms as $room ){
    
                $room_sub_total = $room_sub_total + $room->rate * $res_details->nights;
    
                $rooms[$index]['amount'] = $room->rate * $res_details->nights;
                $rooms[$index]['bill_type'] = 'Room Bill';
                $rooms[$index]['bill_type_id'] = 0;
    
                $index = $index + 1;
    
            }
    
            $bill_total = Additional_bill::where('res_id','=',$res_id)->sum('amount');
    
            $all_bills = Additional_bill::where('res_id','=',$res_id)->leftjoin('rooms','rooms.room_id','=','additional_bills.room_id')
            ->select('additional_bills.*','rooms.room_name')
            ->get();

            $extra_charges_percentage = Cfg_bill_charges::sum('rate');
            
            $extra_charges = Cfg_bill_charges::all();
    
            $sub_total = $room_sub_total + $bill_total;
    
            $discount_rate = ($discount * $room_sub_total / 100);

            $room_sub_total = $room_sub_total - $discount_rate;
    
            $grand_total = $room_sub_total + $bill_total;

            $extra_charges_total = $grand_total * $extra_charges_percentage / 100;

            $grand_total = $grand_total + $extra_charges_total;
            
            $final['reservation_details'] = $res_details;
            $final['reservation_rooms'] = $rooms;
            $final['reservation_room_total'] = $room_sub_total;
            $final['all_bills'] = $all_bills;
            $final['bill_total'] = $bill_total;
            $final['discount'] = $discount;
            $final['discount_rate'] = $discount_rate;
            $final['sub_total'] = $sub_total;
            $final['grand_total'] = $grand_total;
            $final['extra_charges'] = $extra_charges;
            $final['extra_charges_percentage'] = $extra_charges_percentage;
            $final['extra_charges_total'] = $extra_charges_total;

            $final['error_status'] = 0;
            
            return $final;

        }catch(Exception $e){

                
           $final = [
               'status'=>500,
               'error_status'=>2,
               'msg'=>'sometihng went wrong with fetching reservation related bills and room rates',
           ];

            return $final;


        }

    }

   
//---------------------------------------------------------------------------------------------------------

     // this function will only return an array of results for the date passed
    // this is used in the daily_forecast_view table to display in the UI and also 

    // to print the daily forecast

    public function calculate_daily_forecast($checkinday){

        $final = null;

        $checkinday = $checkinday;
        $checkoutday = date('Y-m-d', strtotime($checkinday. ' + 1 days')); 

        try {
            
            $reports = Roomallocation::join('room_reservations','room_reservations.id','=','roomallocations.res_id')
            ->join('agents','agents.id','=','room_reservations.agent_id')
            ->join('rooms','rooms.room_id','=','roomallocations.roomNumber')
            ->select('room_reservations.code','room_reservations.checkinDate'
            ,'room_reservations.checkoutDate','roomallocations.roomNumber','roomallocations.basis'
            ,'roomallocations.date','agents.agentName','agents.agentCode','room_reservations.status')
            ->selectRaw('sum(rooms.room_max_adults + rooms.room_max_children) as mealcount')
            ->where('roomallocations.date','=',$checkinday)
            //->where('roomallocations.date','<',$checkoutday)
            ->with(['get_meal_plan','get_room'])
            ->orderBy('room_reservations.code', 'ASC')
            ->groupBy('roomallocations.roomNumber')
            ->get();


            $count = Roomallocation::where('roomallocations.date','>=',$checkinday)
            ->join('rooms','rooms.room_id','=','roomallocations.roomNumber')
            ->select('basis')
            ->selectRaw('sum(rooms.room_max_adults + rooms.room_max_children) as paxs')
            ->where('roomallocations.date','=',$checkinday)->groupBy('basis')
            ->with(['get_meal_plan'])
            ->get();

            $no_dinner = 0;
            $no_breakfast = 0;
            $no_lunch = 0;

            $meals = [];

            foreach ($count as $row) {
              
                if($row->basis==1){

                    $no_breakfast = $no_breakfast+$row->paxs;

                }

                if($row->basis==2){

                    $no_breakfast = $no_breakfast+$row->paxs;
                    $no_dinner = $no_dinner + $row->paxs;
                    
                }

                if($row->basis==3){

                    $no_breakfast = $no_breakfast+$row->paxs;
                    $no_dinner = $no_dinner + $row->paxs;
                    $no_lunch = $no_lunch + $row->paxs;

                }

            }


            $meals[] =  array('meal_plan' =>1,'name'=>'breakfast','paxs'=>$no_breakfast);
            $meals[] =   array('meal_plan' =>2,'name'=>'lunch','paxs'=>$no_lunch);
            $meals[] =  array('meal_plan' =>3,'name'=>'dinner','paxs'=>$no_dinner);
            

            $final['reports'] = $reports;

            $final['meals_count'] = $meals;

            return $final;

        } catch (\Throwable $th) {
        
            return $final;

        }


    }

//--------------------------------------------------------------------------------------------------------------

    // this function will calculate the forecast of rooms for a given date range
    // this function is used to show data in the room_allocation_forecast_view 

    public function calculate_room_forecast($fromdate,$todate){

        
        try {
            
            $first_date = new DateTime($fromdate);

            $last_date = new DateTime($todate);

            $diff = $first_date->diff($last_date);

            $table = [];

                 
            $res_result = Room::leftjoin('roomallocations','roomallocations.roomNumber','=','rooms.room_id')
            ->leftjoin('room_reservations','room_reservations.id','=','roomallocations.res_id')
            ->where(function($q)use($fromdate,$todate){
                $q->whereBetween('roomallocations.date',[$fromdate,$todate]);
                $q->orwhere('roomallocations.date','=',null);
            })
            ->select('roomallocations.roomNumber','roomallocations.date','rooms.room_id','rooms.room_name','room_reservations.id as res_id','room_reservations.status as res_status')
            ->get();
            
            $booking_result = Room::join('room_booking_allocations','room_booking_allocations.roomNumber','=','rooms.room_id')
            ->select('rooms.room_id','rooms.room_name','room_booking_allocations.date','room_booking_allocations.res_id as book_id')
            ->get();


            for ($i=0; $i<=$diff->d; $i++) {

                $reserved = [];

                $day = date('Y-m-d', strtotime($fromdate. ' + '.$i.'days'));

                $table[$i]['date'] = $day;
                
                foreach ($res_result as $row) {
                    
                    if($row->date==$day){

                        $status = null;

                        switch ($row->res_status) {
                            case 0:
                                
                                $status = 'Pending';

                                break;

                            case 1:
                                
                                $status = 'Reserved';

                                break;
                            
                            case 2:
                                
                                $status = 'Checked IN';

                                break;

                            case 3:
                                
                                $status = 'checked out';

                                break;

                            case 4:

                                $status = 'Cancelled';
                                
                                break;
                         
                            case 5:
                                
                                $status = 'Overwritten';
                                
                                break;
                        }
                        
                        $reserved[] = array('room_id'=>$row->room_id,'room_name' =>$row->room_name,'date'=>$day,'status'=>$status,'type'=>1,'ref_id'=>$row->res_id);

                    }
                    
                }


                foreach ($booking_result as $row) {
                    
                    if($row->date==$day){

                                $status = null;
                        
                        switch ($row->res_status) {
                            case 0:
                                
                                $status = 'Pending';

                                break;

                            case 1:
                                
                                $status = 'Reserved';

                                break;
                            
                            case 2:
                                
                                $status = 'Checked IN';

                                break;

                            case 3:
                                
                                $status = 'checked out';

                                break;

                            case 4:

                                $status = 'Cancelled';
                                
                                break;
                         
                            case 5:
                                
                                $status = 'Overwritten';
                                
                                break;
                        }

                        $reserved[] = array('room_id'=>$row->room_id,'room_name' =>$row->room_name,'date'=>$day,'status'=>$status,'type'=>2,'ref_id'=>$row->book_id);

                    }
                    
                }

                $table[$i]['rooms'] = $reserved;

            }


            $data = [
                'error_status'=>0,
                'status'=>200,
                'data'=>$table,
            ];
            
            return $data;

        } catch (Exception $e) {
            
            $data = [
                'error_status'=>1,
                'status'=>500,
                'error'=>$e->getMessage(),
            ];

            return $data;
        }


    }


//--------------------------------------------------------------------------------------------------------------

    // this function is only used for the PRint the PDF for room forecast,
    // the room allocation forecast view usses the above function


      public function pdf_room_forecast($fromdate,$todate){


        $first_date = new DateTime($fromdate);

        $last_date = new DateTime($todate);

        $diff = $first_date->diff($last_date);

        $table = [];

        try {
            
                $res_result = Room::leftjoin('roomallocations','roomallocations.roomNumber','=','rooms.room_id')
                ->leftjoin('room_reservations','room_reservations.id','=','roomallocations.res_id')
                ->where(function($q)use($fromdate,$todate){
                    $q->whereBetween('roomallocations.date',[$fromdate,$todate]);
                    $q->orwhere('roomallocations.date','=',null);
                })
                ->select('roomallocations.roomNumber','roomallocations.date','rooms.room_id','rooms.room_name','room_reservations.id as res_id',
                'room_reservations.code','room_reservations.status as res_status')
                ->orderBy('roomallocations.date','ASC')
                ->get();


                $booking_result = Room::join('room_booking_allocations','room_booking_allocations.roomNumber','=','rooms.room_id')
                ->join('room_booking','room_booking.booking_reservations_id','=','room_booking_allocations.res_id')
                ->select('rooms.room_id','rooms.room_name','room_booking_allocations.date','room_booking_allocations.res_id as book_id','room_booking.code')
                ->get();


                $rooms = Room::all();

                $test = [];

                for ($i=0; $i<=$diff->d; $i++) {

                    $reserved = [];

                    $day = date('Y-m-d', strtotime($fromdate. ' + '.$i.'days'));

                    $index = 0;

                    foreach ($rooms as $room) {
                    
                    $test[$index]['date'] = $day;
                    $test[$index]['room'] = $room->room_id;
                    $test[$index]['code'] = null;
                    $test[$index]['room_name'] = $room->room_name;
                    $test[$index]['status'] = 'vacant';

                        foreach ($res_result as $row) {
                            
                                if($row->room_id==$room->room_id&&$row->date==$day){

                                    $test[$index]['code'] = $row->code;

                                    $status = null;
                                     
                                    switch ($row->res_status) {
                                        case 0:
                                            
                                            $status = 'Pending';

                                            break;

                                        case 1:
                                            
                                            $status = 'Reserved';

                                            break;
                                        
                                        case 2:
                                            
                                            $status = 'Checked IN';

                                            break;

                                        case 3:
                                            
                                            $status = 'checked out';

                                            break;

                                        case 4:

                                            $status = 'Cancelled';
                                            
                                            break;
                                    
                                        case 5:
                                            
                                            $status = 'Overwritten';
                                            
                                            break;
                                    }
                                    $test[$index]['status'] = $status;

                                }

                        }
                        
                    
                        foreach ($booking_result as $booking) {
                            
                                if($booking->room_id==$room->room_id&&$booking->date==$day){

                                    ++$index;
                                    $test[$index]['room'] = $booking->room_id;
                                    $test[$index]['room_name'] = $booking->room_name;
                                    $test[$index]['date'] = $booking->date;
                                    $test[$index]['code'] = $booking->code;

                                    $status = null;

                                    
                                    $test[$index]['status'] = 'booking';

                                   // dd($index);

                                }

                        }

                        $index++;
                    
                    }

                    $table[] = $test;

                  

                    }

                    //dd($table);
               
            $data = [
                'error_status'=>0,
                'msg'=>'success',
                'table'=>$table,
            ];
        

            return $data;

        } catch (Exception $e) {

            dd($e);
            
            $data = [
                'error_status'=>1,
                'msg'=>'something went wrong',
                'error_msg'=>$e->getMessage(),
            ];

            return $data;

        }

    }

   
}

?>