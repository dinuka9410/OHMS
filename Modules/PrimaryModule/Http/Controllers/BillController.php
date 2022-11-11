<?php

namespace Modules\PrimaryModule\Http\Controllers;

use Modules\PrimaryModule\Models\Additional_bill;

use Modules\PrimaryModule\Models\Invoice;
use Modules\PrimaryModule\Models\RoomReservation;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

use Elibyy\TCPDF\Facades\TCPdf;

use PDF;

use Modules\PrimaryModule\Repositories\CalculatorRepository;

use Illuminate\Routing\Controller;

use function App\GetSystemUserCurrency;
use function App\GetSystemUserCurrency_convertion;
use function App\GetSystemUserSymble;

class BillController extends Controller
{

   
    private $CalculatorRepository;

    public function __construct(CalculatorRepository $CalculatorRepository)
    {
        $this->CalculatorRepository = $CalculatorRepository;
    }
    
    public function billing_view(Request $req){

        $params['pagenames'] = [
            [
                'displayname'=>'Billing',
                'routename'=>'billing_view'
            ],
        ];

        return view('pages/billing_add_view',$params);

    }



    public function Delete_addtional_fac(Request $req){

        $id = $req->id;

        try{

            Additional_bill::where([['additional_bill_id',$id]])->delete();
 
            $data = [
                'status'=>'200',
                'error_status'=>'0',
                'msg'=>'Manual bill delete successfully',
            ];

            return response()->json($data);

        }catch(Exception $e){

            $data = [
                'status'=>'500',
                'error_status'=>'1',
                'msg'=>'unable to delete the additional bills',
                'error_log'=>$e->getMessage(),
            ];

            return response()->json($data);

        }
    }

    public function Load_all_maualbills(Request $req){

        $res_id = $req->res_id;

        try{

            $bill=Additional_bill::with(['getmodule','get_room'])->where([['res_id',$res_id]])->get();
 


            return response()->json($bill);

        }catch(Exception $e){

            $data = [
                'status'=>'500',
                'error_status'=>'1',
                'msg'=>'unable to get the additional bills',
                'error_log'=>$e->getMessage(),
            ];

            return response()->json($data);

        }
    }


    public function add_bill(Request $req){

            $rules = [
                'b_type.*'=>'required',
                'b_code.*'=>'required',
                'b_amount.*'=>'required|numeric',
                'room_id.*'=>'required|numeric',
                'bill_res_id'=>'required|numeric',
            ];
    
            $msg = [
                'b_type.*.required'=>'please select a bill type',
                'b_code.*.required'=>'please enter the bill code',
                'b_amount.*.required'=>'please enter the bill amount',
                'room_id.*.required'=>'please select a room no',
                'res_id.required'=>'please provide a reservation ID',
            ];
    
            $validation = Validator::make($req->all(),$rules,$msg)->validate();

            DB::beginTransaction();

            $user = Auth::user();

            $Cyrate= GetSystemUserCurrency_convertion(1);

            
            try{

                $no_rows = count($req->b_code);

                $billlist = [];

                for ($i=0; $i <$no_rows; $i++) { 
                    
                    $amountfinal = $req->b_amount[$i] * $Cyrate;

                    $data['bill_no'] = strtoupper($req->b_code[$i]);
                    $data['bill_type'] = $req->b_type[$i];
                    $data['res_id'] = $req->bill_res_id;
                    $data['room_id'] = $req->room_id[$i];
                    $data['created_by'] = $user->id;
                    $data['date'] = date('y-m-d');
                    $data['amount'] = $amountfinal;
                    $data['department'] = $req->b_type[$i];
                    $data['created_at'] = Carbon::now();
                    $data['updated_at'] = null;

                    $billlist[] = $data;

                }

                Additional_bill::insert($billlist);

                DB::commit();

                $data = [
                    'status'=>'200',
                    'error_status'=>'0',
                    'msg'=>'Manual bill added successfully',
                ];

                //return redirect()->route('room_reservation_view')->with('status',$data);

            }catch(Exception $e){

                DB::rollback();

                $data = [
                    'status'=>'500',
                    'error_status'=>'1',
                    'msg'=>'unable to process the bill',
                    'error_log'=>$e->getMessage(),
                ];

                //return redirect()->route('room_reservation_view')->with('status',$data);

            }


    }


    //-------------------------------------------------------------

    // this function will return the total of all the bills into a reservation and 
    // also return all the bills regarding the reservation rooms plus grand total sub total and lot more
    // use this function calculations regrding reservation

    public function get_reservation_total(Request $req){

        $res_id = $req->res_id;

        try{

            $info = $this->CalculatorRepository->get_reservation_total_bill($res_id,0);


            $data = [
                'status'=>'200',
                'error_status'=>'0',
                'msg'=>'success',
                'info'=>$info,

            ];

            return response()->json($data);

        }catch(Exception $e){

            $data = [
                'status'=>'500',
                'error_status'=>'1',
                'msg'=>'unable to get the additional bills',
                'error_log'=>$e->getMessage(),
            ];

            return response()->json($data);

        }

    }


    // =-------------- this function will print the GRC of the reservation ------//


        public function print_grc(Request $req){

            $rules = [
                'id'=>'required',
            ];

            $msg = [
                'id.required'=>'please provide a valid reservation id'
            ];

                    $validation = Validator::make($req->all(),$rules,$msg)->validate();


                                   // pass the reservation id and discount
                    $info =$this->CalculatorRepository->get_reservation_total_bill($req->id,0);

                    // if there are no errors and if the reservation is on confirm then allow to print
                    if($info['error_status']==0&&$info['reservation_details']->status==1){

                        $this->grcprint($info);

                    }else{

                        return redirect()->route('dashboard')->with('status',$info);

                    }
        
           
        }


    //-------------------------------------------------------------------------------




    //---------------------------------------------------------------


    public function print_res_bill(Request $req){

                       // pass the reservation id and discount
        $info = $this->CalculatorRepository->get_reservation_total_bill($req->id,0);

        if($info['error_status']==0){
           
            $this->print_room_bill($info,'I');

        }else{

            return redirect()->route('dashboard')->with('status',$info);

        }

    }

    //--------------------------------------------------------------



    // this function will print the final bill with discount and service charges includes

    public function print_final_res_bill(Request $req){

        try {

           $reservation = RoomReservation::where('id','=',$req->id)->first();

           if($reservation!=null&&$reservation->status==3){
            
                $inv_details = Invoice::where('res_id','=',$req->id)->first();
               
                $info = $this->CalculatorRepository->get_reservation_total_bill($req->id,(int)$inv_details->discount);
                $info['db_extra_charges'] = $inv_details->extra_charges;
                
                $data=Invoice::where('res_id','=',$req->id)->with(['get_invo_romms'])->get();
                //dd($data);

                $this->final_print_room_bill($info,'I',$data);

           }else{

                $data = [
                    'status'=>'400',
                    'error_status'=>'2',
                    'msg'=>'something went wrong unable to view the final bill'
                ];

                return redirect()->route('room_reservation_view')->with('status',$data);

           }


        } catch (Exception $e) {
            
            $data = [
                'status'=>'400',
                'error_status'=>'2',
                'msg'=>'something went wrong unable to view the final bill'
            ];

            return redirect()->route('room_reservation_view')->with('status',$data);

        }

    }


//---------------------------------------------------------------------------------------------------------

     // this function will print a pdf GRC

     private function grcprint($info){

                 // even though the text editor shows an error, don't worry, it works perfectly fine
                // don't remove the below line, it will break then
                // if it works, do not touch it

                $pdf = new PDF;

                $res_details = $info['reservation_details'];

                $rooms = $info['reservation_rooms'];

                $room_total = $info['reservation_room_total'];

                $currrate = GetSystemUserCurrency(1);

                
                $agreement = '<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Maxime mollitia,
                molestiae quas vel sint commodi repudiandae consequuntur voluptatum laborum
                numquam blanditiis harum quisquam eius sed odit fugiat iusto fuga praesentium
                optio, eaque rerum! Provident similique accusantium nemo autem. Veritatis
                obcaecati tenetur iure eius earum ut molestias architecto voluptate aliquam
                nihil, eveniet aliquid culpa officia aut! Impedit sit sunt quaerat, odit,
                tenetur error, harum nesciunt ipsum debitis quas aliquid.</p>';

                $agreement2 = '<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Maxime mollitia,
                molestiae quas vel sint commodi repudiandae consequuntur voluptatum laborum
                numquam blanditiis harum quisquam eius sed odit fugiat iusto fuga praesentium
                optio, eaque rerum! Provident similique accusantium nemo autem. Veritatis
                obcaecati tenetur iure eius earum ut molestias architecto voluptate aliquam
                nihil, eveniet aliquid culpa officia aut! Impedit sit sunt quaerat, odit,
                tenetur error, harum nesciunt ipsum debitis quas aliquid.</p>';

                $table = ' <table border="0" cellspacing="5" cellpadding="5">

                    <thead class="thead-dark" >
                        <tr  >
                            <th>Room No.</th>
                            <th>Room Type</th>
                            <th>Room Category</th>
                            <th>Meal Plan</th>
                            <th>Rate (per night) </th>
                            <th>Amount</th>

                        </tr>
                    </thead>

                <tbody>';

                foreach($rooms as $row){

                    $finalrate = ($row->rate) * $currrate;

                    $finalamount = ($row->amount) * $currrate;
                    
                    $table .= '<tr><td>'.$row->room_name.'</td><td>'.$row->get_room_type->room_type_Select.'</td><td>'.$row->get_category->room_categories_name.'</td><td>'.$row->mealPlanCode.'</td><td>'.GetSystemUserSymble().' '.round($finalrate,2).'</td><td>'.GetSystemUserSymble().' '.round($finalamount,2).'</td></tr>';
                
                }

                $finalroomtotal = ($room_total) * $currrate;

                $table .= '<tr><td></td><td></td><td></td><td></td><td>Room Total</td><td>'.GetSystemUserSymble().' '.round($finalroomtotal,2).'</td></tr>';

            $table .='  

                </tbody>
                
            </table>';

                $pdf::addPage();
                //-------- header part ---///
                $pdf::SetFont('Helvetica','',18);
                $pdf::cell(190,3,'Hotel Lagone Kandy',0,1,'C');
                $pdf::SetFont('Helvetica','',10);
                $pdf::cell(190,3,'ramanayake mawatha colombo 2 kandy ',0,1,'C');
                $pdf::cell(190,7,'081 - 4221338 / 081 - 4221337',0,1,'C');

                $pdf::Ln();
                //------- reservation details ----- //
                $pdf::cell(190,0,'Reservation code : '.$res_details->code,0,1);
                $pdf::cell(190,8,'Guest Name : '.$res_details->get_guest->guestFname." ".$res_details->get_guest->guestLname,0,1);
                $pdf::cell(190,8,'Check in Date : '.$res_details->checkinDate,0,1);
                $pdf::cell(190,8,'Check out Date : '.$res_details->checkoutDate,0,1);
                $pdf::cell(190,8,'Travel Agent : '.$res_details->get_travel_agent->agentName,0,1);
                $pdf::cell(190,8,'No of nights : '.$res_details->nights,0,1);
                // --- agreement area -------//
                $pdf::cell(190,5,'',0,1,'C');
                $pdf::writeHTMLCell(190,0,'','',$agreement,0);
                $pdf::Ln();
                // ------ table for room numbers --- //
                $pdf::cell(190,5,'',0,1,'C');
                $pdf::writeHTMLCell(192,0,9,'',$table,0);
                $pdf::Ln();
                $pdf::cell(190,7,'',0,1,'C');
                $pdf::writeHTMLCell(190,10,'','',$agreement2,0);

                $pdf::setFooterCallback(function($pdf){

                    // Position at 15 mm from bottom
                    $pdf->SetY(-20);
                    // Set font
                    $pdf->SetFont('helvetica', 'I', 8);
                    $pdf->Cell(0, 0,'By signing to this agreement you are agreeing to the above conditions', 0, false, '', 0, '', 0, false, 'T', 'M');
                    $pdf->Ln();
                    $pdf->Cell(10, 16,'Guest : _______________________             Receptionist : _________________________', 0, false, '', 0, '', 0, false, 'T', 'M');
                    $pdf->Ln();
                    });


                $pdf::lastPage();
                $pdf::output();

        }

//---------------------------------------------------------------------------------------------------------

private function final_print_room_bill($info,$option,$data){

    $pdf = new PDF;

    $rooms = $info['reservation_rooms'];
    $res_details = $info['reservation_details'];
    $all_bills = $info['all_bills'];
    $extra_charges = $info['extra_charges'];
    
    $pdf::addPage();
    //-------- header part ---///
    $pdf::SetFont('Helvetica','',18);
    $pdf::cell(190,10,'Hotel Lagone Kandy',0,1,'C');
    $pdf::SetFont('Helvetica','',10);
    $pdf::cell(190,3,'ramanayake mawatha colombo 2 kandy ',0,1,'C');
    $pdf::cell(190,7,'081 - 4221338 / 081 - 4221337',0,1,'C');
    $pdf::SetFont('Helvetica','',13);
    $pdf::cell(190,3,'---- Reservation Bill ----',0,1,'C');

    $pdf::Ln();
    $pdf::SetFont('Helvetica','',10);

    //------- reservation details ----- //
    $pdf::cell(190,8,'Reservation code :'.$res_details->code,0,1);
    $pdf::cell(190,8,'Guest Name : '.$res_details->get_guest->guestFname." ".$res_details->get_guest->guestLname,0,1);
    $pdf::cell(190,8,'Check in Date : '.$res_details->checkinDate,0,1);
    $pdf::cell(190,8,'Check out Date : '.$res_details->checkoutDate,0,1);
    $pdf::cell(190,8,'Travel Agent : '.$res_details->get_travel_agent->agentName,0,1);
    $pdf::cell(190,8,'No of nights : '.$res_details->nights,0,1);

    //--------- start of body -----------------------------//
    foreach($data as $data){
        $pdf::Ln();
        $pdf::cell(190,10,$data->invo_code,1,'C');
        $pdf::Ln();
        $pdf::cell(30,10,'Bill Type',1,'C');
        $pdf::cell(20,10,'Room No',1,'C');
        $pdf::cell(30,10,'Total',1,'C');
        $pdf::cell(25,10,'Disscount',1,'C');
        $pdf::cell(20,10,'Extra Total',1,'C');
        $pdf::cell(30,10,'Sub Total',1,'C');
        $pdf::cell(35,10,'Total With Discount',1,'C');

        $currrate = GetSystemUserCurrency(1);
        foreach($data->get_invo_romms as $room){
        $pdf::Ln();
        $pdf::cell(30,10,'Room bill',1,'C');
        $pdf::cell(20,10,$room->get_match_romms['room_name'],1,'C');
        $pdf::cell(30,10,GetSystemUserSymble().' '.$room->total,1,'C');
        if($room->diss_Type== 1)
        {
            $pdf::cell(25,10,$room->disscount.'%',1,'C');
        }
        else
        {
            $pdf::cell(25,10,GetSystemUserSymble().' '.$room->disscount.'.00',1,'C');
        }
        $pdf::cell(20,10,GetSystemUserSymble().' '.$room->addtional_total.'.00',1,'C');
        $pdf::cell(30,10,GetSystemUserSymble().' '.$room->finaltotal_Total.'.00',1,'C');
        $pdf::cell(35,10,GetSystemUserSymble().' '.$room->subtotal_Total.'.00',1,'C');
        
        }
        $pdf::Ln();
        $pdf::cell(190,7,'Total Addtional Bill : '.GetSystemUserSymble().' '.$data->final_addtional.' ',0,1);
        $pdf::cell(190,7,'Full Bill Disscount : '.GetSystemUserSymble().' '.$data->discount.' ',0,1);
        $pdf::cell(190,7,'Total Bill : '.GetSystemUserSymble().' '.$data->final_room.' ',0,1);
        $pdf::cell(190,7,'Total With Addtional Bill : '.GetSystemUserSymble().' '.$data->final_subtotal.' ',0,1);
        $pdf::cell(190,7,'Sub Total : '.GetSystemUserSymble().' '.$data->final_grandtotal.' ',0,1);
        $pdf::Ln();
    
    }

    $currrate = GetSystemUserCurrency(1);
   
    $pdf::Ln();
    $pdf::cell(40,10,'Bill No',1,'C');
    $pdf::cell(40,10,'Bill Type',1,'C');
    $pdf::cell(30,10,'room No',1,'C');
    $pdf::cell(40,10,'Total',1,'C');
    $pdf::cell(40,10,' Sub Total',1,'C');

        foreach($all_bills as $bill){

            $pdf::Ln();

            $pdf::cell(40,10,$bill->bill_no,1,'C');
            
            switch ($bill->bill_type) {

                case 0:
                    $pdf::cell(40,10,'Room Bill',1,'C');
                    break;

                case 1:
                    $pdf::cell(40,10,'House keeping bill',1,'C');
                    break;
                case 2:
                    $pdf::cell(40,10,'Additional service bill',1,'C');
                    break;  
                    
                case 3:
                    $pdf::cell(40,10,'Kitchen bill',1,'C');
                    break;

                case 4:
                    $pdf::cell(80,10,'Bar Bill',1,'C');
                    break;

            }

            if($bill->room_id==0){

                $pdf::cell(30,10,'All Rooms',1,'C');
                
            }else{

                $pdf::cell(30,10,$bill->room_name,1,'C');
                
            }

            

            $finalamount = ($bill->amount) * $currrate;
            
            $pdf::cell(40,10,GetSystemUserSymble()." ".round($finalamount,2),1,'C');
            $pdf::cell(40,10,GetSystemUserSymble()." ".round($finalamount,2),1,'C');

        }



    // $pdf::Ln();

    // $pdf::cell(190,10,'',0,1);

    // $subtotal = ($info['sub_total']) * $currrate;

    // $pdf::cell(190,7,'Sub Total : '.GetSystemUserSymble()." ".round($subtotal,2),0,1);
    
    // if($info['discount']!=0){

    //     $pdf::cell(190,7,'Discount : '.$info['discount'].' %',0,1);
        
    // }

    // if($info['extra_charges']!=null){

    //     foreach ($extra_charges as $row) {
        
    //         $pdf::cell(190,7,$row->name.' : '.$row->rate.' %',0,1);

    //    }

    // }else{

    //     $pdf::cell(190,7,'Extra Charges : '.$info['db_extra_charges'].' %',0,1);

    // }

    // $grandtotal = ($info['grand_total']) * $currrate;

    // $pdf::cell(190,7,'Grand Total : '.GetSystemUserSymble().round($grandtotal,2),0,1);
       
    // ---------- end of body --------------------//


    // ---------- footer part ---//

    $pdf::setFooterCallback(function($pdf){

        // Position at 15 mm from bottom
         $pdf->SetY(-20);
         // Set font
         $pdf->SetFont('helvetica', 'I', 8);
         $pdf->cell(190,10,'Thank you and come again!',0,1,'C');
         $pdf->Ln();

        });


    $pdf::lastPage();
    return $pdf::output('output.pdf',$option);



}


    // this function will print a PDF with reservation room bill + additional features

    private function print_room_bill($info,$option){

        $pdf = new PDF;

        $rooms = $info['reservation_rooms'];
        $res_details = $info['reservation_details'];
        $all_bills = $info['all_bills'];
        $extra_charges = $info['extra_charges'];
        
        $pdf::addPage();
        //-------- header part ---///
        $pdf::SetFont('Helvetica','',18);
        $pdf::cell(190,10,'Hotel Lagone Kandy',0,1,'C');
        $pdf::SetFont('Helvetica','',10);
        $pdf::cell(190,3,'ramanayake mawatha colombo 2 kandy ',0,1,'C');
        $pdf::cell(190,7,'081 - 4221338 / 081 - 4221337',0,1,'C');
        $pdf::SetFont('Helvetica','',13);
        $pdf::cell(190,3,'---- Reservation Bill ----',0,1,'C');

        $pdf::Ln();
        $pdf::SetFont('Helvetica','',10);

        //------- reservation details ----- //
        $pdf::cell(190,8,'Reservation code :'.$res_details->code,0,1);
        $pdf::cell(190,8,'Guest Name : '.$res_details->get_guest->guestFname." ".$res_details->get_guest->guestLname,0,1);
        $pdf::cell(190,8,'Check in Date : '.$res_details->checkinDate,0,1);
        $pdf::cell(190,8,'Check out Date : '.$res_details->checkoutDate,0,1);
        $pdf::cell(190,8,'Travel Agent : '.$res_details->get_travel_agent->agentName,0,1);
        $pdf::cell(190,8,'No of nights : '.$res_details->nights,0,1);

        //--------- start of body -----------------------------//

        $pdf::Ln();
        $pdf::cell(50,10,'Bill No',1,'C');
        $pdf::cell(40,10,'Bill Type',1,'C');
        $pdf::cell(30,10,'Room No',1,'C');
        $pdf::cell(30,10,'Rate',1,'C');
        $pdf::cell(40,10,'Amount',1,'C');

        $currrate = GetSystemUserCurrency(1);
        
            foreach($rooms as $room){

                $pdf::Ln();
                $pdf::cell(50,10,'',1,'C');
                $pdf::cell(40,10,$room->bill_type,1,'C');
                $pdf::cell(30,10,$room->room_name.' '. $room->mealPlanCode,1,'C');
                $finalrate = ($room->rate) * $currrate;
                $finalamount = ($room->amount) * $currrate;
                $pdf::cell(30,10,GetSystemUserSymble()." ".round($finalrate,2),1,'C');
                $pdf::cell(40,10,GetSystemUserSymble()." ".round($finalamount,2),1,'C');                

            }


            foreach($all_bills as $bill){

                $pdf::Ln();

                $pdf::cell(50,10,$bill->bill_no,1,'C');
                
                switch ($bill->bill_type) {

                    case 0:
                        $pdf::cell(40,10,'Room Bill',1,'C');
                        break;

                    case 1:
                        $pdf::cell(40,10,'House keeping bill',1,'C');
                        break;
                    case 2:
                        $pdf::cell(40,10,'Additional service bill',1,'C');
                        break;  
                        
                    case 3:
                        $pdf::cell(40,10,'Kitchen bill',1,'C');
                        break;

                    case 4:
                        $pdf::cell(40,10,'Bar Bill',1,'C');
                        break;
    
                }

                if($bill->room_id==0){

                    $pdf::cell(30,10,'All Rooms',1,'C');
                    
                }else{

                    $pdf::cell(30,10,$bill->room_name,1,'C');
                    
                }

                

                $finalamount = ($bill->amount) * $currrate;
                
                $pdf::cell(30,10,GetSystemUserSymble()." ".round($finalamount,2),1,'C');
                $pdf::cell(40,10,GetSystemUserSymble()." ".round($finalamount,2),1,'C');

            }



        $pdf::Ln();

        $pdf::cell(190,10,'',0,1);

        $subtotal = ($info['sub_total']) * $currrate;

        $pdf::cell(190,7,'Sub Total : '.GetSystemUserSymble()." ".round($subtotal,2),0,1);
        
        if($info['discount']!=0){

            $pdf::cell(190,7,'Discount : '.$info['discount'].' %',0,1);
            
        }

        if($info['extra_charges']!=null){

            foreach ($extra_charges as $row) {
            
                $pdf::cell(190,7,$row->name.' : '.$row->rate.' %',0,1);
   
           }

        }else{

            $pdf::cell(190,7,'Extra Charges : '.$info['db_extra_charges'].' %',0,1);

        }

        $grandtotal = ($info['grand_total']) * $currrate;

        $pdf::cell(190,7,'Grand Total : '.GetSystemUserSymble().round($grandtotal,2),0,1);
           
        // ---------- end of body --------------------//


        // ---------- footer part ---//

        $pdf::setFooterCallback(function($pdf){

            // Position at 15 mm from bottom
             $pdf->SetY(-20);
             // Set font
             $pdf->SetFont('helvetica', 'I', 8);
             $pdf->cell(190,10,'Thank you and come again!',0,1,'C');
             $pdf->Ln();
 
            });


        $pdf::lastPage();
        return $pdf::output('output.pdf',$option);



    }

}
