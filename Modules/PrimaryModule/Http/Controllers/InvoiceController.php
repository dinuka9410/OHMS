<?php

namespace Modules\PrimaryModule\Http\Controllers;
use Illuminate\Routing\Controller;
use Modules\PrimaryModule\Models\Invoice;
use Modules\PrimaryModule\Models\invoice_rooms;
use Modules\PrimaryModule\Models\Invoice_details;
use Modules\PrimaryModule\Models\roomReservation;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\PrimaryModule\Repositories\CalculatorRepository;
use Carbon\Carbon;
use Modules\PrimaryModule\Models\Roomallocation;
class InvoiceController extends Controller
{

    private $CalculatorRepository;

    public function __construct(CalculatorRepository $CalculatorRepository)
    {
        $this->CalculatorRepository = $CalculatorRepository;
    }
    
    // this function will add the reservation related to bills to the invoice and invoice details
    // table

    public function add_to_invoice(Request $req){



        $rules = [
            'res_id'=>'required',
            'toatl_discount'=>'required',
            'debtor_type'=>'required'
        ];

        $msg = [
            'res_id.required'=>'please provide a valid reservation id',
            'toatl_discount.required'=>'discount should not be empty',
            'debtor_type.required'=>'please select bill paying type'
        ];

        $validation = validator::make($req->all(),$rules,$msg)->validate();

        try {
        
            DB::beginTransaction();

            $info = $this->CalculatorRepository->get_reservation_total_bill($req->res_id,$req->discount);

            $user = Auth::user();
            

            if($req->debtor_type==1){

                $bebtor_id = $info['reservation_details']->guest_id;
            

            }else{

                $bebtor_id = $info['reservation_details']->agent_id;

            }
         
            $invoice_id_code = $this->CalculatorRepository->generateID('INV','invoices','invo_code',Carbon::now());
   
            try {
                
            if(isset($req->checkbox)){

            $invoice = Invoice::insertGetId([
                'res_id'=>$req->res_id,
                'debtor_type'=>$req->debtor_type,
                'invo_code'=>$invoice_id_code,
                'debtor_id'=>$bebtor_id,
                'created_by'=>$user->id,
                'final_room'=>$req->final_room,
                'final_addtional'=>$req->final_addtional,
                'final_subtotal'=>$req->final_subtotal,
                'final_grandtotal'=>$req->final_grandtotal,
                'discount'=>$req->final_discount_amount,
                'discount_type'=>$req->final_presentage,
                'invoice_date'=>date("Y-m-d"),

            ]);
            $check_out_count=0;
            foreach($req->checkbox as $row){

                $check_out_count++;
                $disscount_rooms=$req->room_diss[$row];
                $checkout_date_room=$req->ch_out[$row];
                
                invoice_rooms::insert([
                    'invoice_id'=>$invoice,
                    'Room_id'=>$row,
                    'disscount'=>$disscount_rooms,
                    'diss_Type'=>$req['roomDiss'.$row],
                    'checkout_date'=>$checkout_date_room,
                    'rate'=>$req['subtotrooms'.$row],
                    'total'=>$req['subtotrooms'.$row],
                    'addtional_total'=>$req['add_toatal'.$row],
                    'finaltotal_Total'=>$req['roomgradd_tot'.$row],
                    'subtotal_Total'=>$req['withdiss_total'.$row],
                ]);
                $checkindate=$info['reservation_details']['checkoutDate'];

                $C_date=Carbon::parse($checkindate);
                $O_date=Carbon::parse($checkout_date_room);
                $n_days=$O_date->diffInDays($C_date);

                while($n_days>0)
                {
                    $allocation_lsit=  Roomallocation::where('res_id','=',$req->res_id)
                    ->where('roomNumber','=',$row)
                    ->orderBy('id', 'desc')->get()->first();
                    $n_days--;

                    Roomallocation::where('id','=',$allocation_lsit->id)->delete();
                }
            }
            if($req->room_count == $check_out_count)
            {
                roomReservation::where('id','=',$req->res_id)->update(['status'=>3]);
                
            }
            if(($check_out_count + $req->count) == $req->room_count)
            {
                roomReservation::where('id','=',$req->res_id)->update(['status'=>3]);
            }

           
        }
        else{
            $data = [
                'status'=>'404',
                'error_status'=>'1',
                'msg'=>'Please select rooms'
            ];
            return redirect()->back()->with('status',$data);
        }

        } catch (Exception $e) {
            $data = [
                'status'=>'404',
                'error_status'=>'1',
                'msg'=>'Something went wrong'
            ];
            return redirect()->back()->with('status',$data);

        }
            
            DB::commit();

            $data = [
                'status'=>'200',
                'error_status'=>'0',
                'msg'=>'Checkout successfully'
            ];
    
            return redirect()->route('room_reservation_view')->with('status',$data);
            
        } catch (Exception $e) {
        
            DB::rollBack();


            $data = [
                'status'=>'404',
                'error_status'=>'1',
                'msg'=>'unable to checkout this reservation, please try again'
            ];

            return redirect()->back()->with('status',$data);

        }

    }
    
}
