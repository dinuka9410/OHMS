<?php

namespace Modules\PrimaryModule\Http\Controllers;

use Illuminate\Routing\Controller;

use Modules\PrimaryModule\Models\Room;
use Illuminate\Http\Request;

use Modules\PrimaryModule\Repositories\CalculatorRepository;

use Elibyy\TCPDF\Facades\TCPdf;
use PDF;

class ReservationReportController extends Controller
{
    

    private $CalculatorRepository;

    public function __construct(CalculatorRepository $CalculatorRepository)
    {
        $this->CalculatorRepository = $CalculatorRepository;
    }
 
    public function daily_forecast_view(Request $req){

        $params['pagenames'] = [
            [
                'displayname'=>'Daily Forecast',
                'routename'=>'daily_forecast_view'
            ],
        ];

       if(isset($req->date)){

            $checkinday = $req->date;

            $data =$this->CalculatorRepository->calculate_daily_forecast($checkinday);
            
            $params['reports'] = $data['reports'];

            $params['meals_count'] = $data['meals_count'];

            //dd($req['meals_count']);

            $params['checkinday'] = $checkinday;

            return view('primarymodule::pages/reports_daily_forecast_view',$params);

       }else{

            $checkinday = date('Y-m-d');

            //dd($checkinday);

            $data = $this->CalculatorRepository->calculate_daily_forecast($checkinday);
            
            $params['reports'] = $data['reports'];

            $params['meals_count'] = $data['meals_count'];

            //dd($req['meals_count']);

            $params['checkinday'] = $checkinday;

            return view('primarymodule::pages/reports_daily_forecast_view',$params);

       }

    }


    public function print_daily_forecast(Request $req){

        $checkinday = $req->date;

        $data = $this->CalculatorRepository->calculate_daily_forecast($checkinday);

        $data['checkinday'] = $checkinday;

        $this->print_forecast($data);

    }

//--------------------------------------------------------------------------------------------------------


    public function room_allocation_forecast_view(Request $req){

        $params['pagenames'] = [
            
            [
                'displayname'=>'Room Allocation Forecast',
                'routename'=>'room_allocation_forecast_view'
            ],
        ];

        $params['s_date'] = $req->s_date;
        $params['e_date'] = $req->e_date;
      
        if(isset($req->s_date)&&isset($req->e_date)){

            $fromdate = $req->s_date;

            $todate = $req->e_date;


        }else{

            $fromdate = date('Y-m-d');

            $todate = date('Y-m-d', strtotime($fromdate. ' + 14 days')); 

        }



        $data = $this->CalculatorRepository->calculate_room_forecast($fromdate,$todate);
        
   
        if($data['error_status']==0){

            $params['rooms'] = Room::all();
            $params['table'] = $data['data'];

    
            return view('primarymodule::pages/reports_room_allocation_view',$params);

        }else{

           
            $data = [
                'status'=>'404',
                'error_status'=>'1',
                'msg'=>'unable to load room forecast'
            ];

            return redirect()->route('reports_view')->with('status',$data);

        }
    

    }


    public function print_room_allocation_forecast(Request $req){

        if(isset($req->s_date)&&isset($req->e_date)){

            $fromdate = $req->s_date;

            $todate = $req->e_date;


        }else{

            $fromdate = date('Y-m-d');

            $todate = date('Y-m-d', strtotime($fromdate. ' + 14 days')); 

        }
        
        $data = $this->CalculatorRepository->pdf_room_forecast($fromdate,$todate);
        
        if($data['error_status']==0){

            $final['table'] = $data['table'];

            $final['from_date'] = $fromdate;

            $final['to_date'] = $todate;


            $this->print_room_forecast($final);

        }else{

            $data = [
                'status'=>'404',
                'error_status'=>'1',
                'msg'=>'unable to load room forecast'
            ];

            return redirect()->route('room_allocation_forecast_view')->with('status',$data);

        }
        

    }


//---------------------------------------------------------------------------------------------------------

      // this function will print a PDF with  the daily forecast for the given date

      private function print_forecast($data){

        
        // even though the text editor shows an error, don't worry, it works perfectly fine
        // don't remove the below line, it will break then
        // if it works, do not touch it

        $pdf = new PDF;
        $data = $data;

        $pdf::addPage();
        //-------- header part ---///
        $pdf::SetFont('Helvetica','',18);
        $pdf::cell(190,10,'Hotel Lagone Kandy',0,1,'C');
        $pdf::SetFont('Helvetica','',10);
        $pdf::cell(190,3,'ramanayake mawatha colombo 2 kandy ',0,1,'C');
        $pdf::cell(190,7,'081 - 4221338 / 081 - 4221337',0,1,'C');
        $pdf::SetFont('Helvetica','',13);
        $pdf::cell(190,3,'---- Daily Forecast ----',0,1,'C');

        $pdf::Ln();
        $pdf::SetFont('Helvetica','',10);

        // ------- reservation details ----- //
        $pdf::cell(190,8,'date : '.$data['checkinday'],0,1);
        
        foreach ($data['meals_count'] as $row) {

            if($row['meal_plan']==1){

                $pdf::cell(190,8,"Tommorrow Breakfast Paxs : ".$row['paxs'],0,1);

            }

            if($row['meal_plan']==2){

                $pdf::cell(190,8,"Tommorrow Lunch Paxs : ".$row['paxs'],0,1);

           }

           if($row['meal_plan']==3){

            $pdf::cell(190,8,"Tonight Dinner Paxs : ".$row['paxs'],0,1);

          }
            
           // TCPdf::cell(190,8,$row['name'].": ".$row['paxs'],0,1);

        }


        //--------- start of body -----------------------------//

        $pdf::Ln();
        $pdf::cell(35,10,'Reservation ID',1,'C');
        $pdf::cell(20,10,'Room No',1,'C');
        $pdf::cell(15,10,'Basis',1,'C');
        $pdf::cell(15,10,'Paxs',1,'C');
        $pdf::cell(20,10,'Agent',1,'C');
        $pdf::cell(30,10,'Checkin Date',1,'C');
        $pdf::cell(30,10,'Checkout Date',1,'C');
        $pdf::cell(20,10,'Status',1,'C');

        
        
            foreach($data['reports'] as $row){

                $pdf::Ln();
                $pdf::cell(35,10,$row->code,1,'C');
                $pdf::cell(20,10,$row->get_room->room_name,1,'C');
                $pdf::cell(15,10,$row->get_meal_plan->mealPlanCode,1,'C');
                $pdf::cell(15,10,$row->mealcount,1,'C');
                $pdf::cell(20,10,$row->agentCode,1,'C');
                $pdf::cell(30,10,$row->checkinDate,1,'C');
                $pdf::cell(30,10,$row->checkoutDate,1,'C');
               
                switch ($row->status) {

                    case 0:
                        $pdf::cell(20,10,'Pending',1,'C');
                        break;

                    case 1:
                        $pdf::cell(20,10,'Confirmed',1,'C');
                        break;
                    case 2:
                        $pdf::cell(20,10,'Checked IN',1,'C');
                        break;  
                        
                    case 3:
                        $pdf::cell(20,10,'Check out',1,'C');
                        break;

                    case 4:
                        $pdf::cell(20,10,'Cancelled',1,'C');
                        break;
    
                }
                

            }



        $pdf::Ln();

            
        // ---------- end of body --------------------//


        // ---------- footer part ---//

        $pdf::setFooterCallback(function($pdf){

            // Position at 15 mm from bottom
             $pdf->SetY(-20);
             // Set font
             $pdf->SetFont('helvetica', 'I', 8);
             $pdf->cell(190,10,'Thank you !',0,1,'C');
             $pdf->Ln();
 
            });


        $pdf::lastPage();
        return $pdf::output('output.pdf','I');


     }

//-----------------------------------------------------------------------------------------------------------

     
     // this will print the room forecast page 

     public function print_room_forecast($data){

        $data = $data;

           // even though the text editor shows an error, don't worry, it works perfectly fine
        // don't remove the below line, it will break then
        // if it works, do not touch it
        
        $pdf = new PDF;

        $pdf::addPage();
        //-------- header part ---///
        $pdf::SetFont('Helvetica','',18);
        $pdf::cell(190,10,'Hotel Lagone Kandy',0,1,'C');
        $pdf::SetFont('Helvetica','',10);
        $pdf::cell(190,3,'ramanayake mawatha colombo 2 kandy ',0,1,'C');
        $pdf::cell(190,7,'081 - 4221338 / 081 - 4221337',0,1,'C');
        $pdf::SetFont('Helvetica','',13);
        $pdf::cell(190,3,'---- Room Forecast ----',0,1,'C');

        $pdf::Ln();
        $pdf::SetFont('Helvetica','',10);

    
        //------- details ----- //
        $pdf::cell(190,8,'Start Date: '.$data['from_date'],0,1);
        $pdf::cell(190,8,'End Date : '.$data['to_date'],0,1);


        // body-----------------------------------------


        $pdf::Ln();
        $pdf::cell(50,10,'Date',1,'C');
        $pdf::cell(50,10,'Reference Code',1,'C');
        $pdf::cell(50,10,'Room No',1,'C');
        $pdf::cell(30,10,'Status',1,'C');


        foreach ($data['table'] as $records) {
           
            foreach ($records as $row ) {
                
                $pdf::Ln();
                $pdf::cell(50,10,$row['date'],1,'C');
                $pdf::cell(50,10,$row['code'],1,'C');
                $pdf::cell(50,10,$row['room_name'],1,'C');
                $pdf::cell(30,10,$row['status'],1,'C');

            }
            
        }



        // end of boddy---------------------------------


        // ---------- footer part ---//

        $pdf::setFooterCallback(function($pdf){

            // Position at 15 mm from bottom
             $pdf->SetY(-20);
             // Set font
             $pdf->SetFont('helvetica', 'I', 8);
             $pdf->Ln();
 
            });


        $pdf::lastPage();
        return $pdf::output('output.pdf','I');

     }


}
