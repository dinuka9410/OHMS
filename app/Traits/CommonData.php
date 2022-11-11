<?php
namespace App\Traits;

use DateTime;
use DateInterval;

trait CommonData{

    public $dtdate;
    public $dtdatetime;
    public $dtdatetimesp;
    public $dtcyf;
    public $dtcy;
    public $dtcm;
    public $dtcmt;
    public $dtcd;
    public $dtwn;
    public $dtdy;
    public $dtacs;
    public $dtsdom;
    public $dtldom;
    public $ftdate;

    public $usid;
    public $brid;

    public function __construct(){
        $this->set_defaults();
    }
    //Set Default Datas
    private function set_defaults(){
        //Genarate Data
        $date = new DateTime();
        //$date->setTimezone( $timezone );
        $dtdate = $date->format('Y-m-d');
        $dtdatetime = $date->format('Y-m-d H:i:s');
        $dtdatetimesp = $date->format('Y-m-d h:i A');
        $dtcyf = $date->format('Y');
        $dtcy = $date->format('y');
        $dtcm = $date->format('m');
        $dtcmt = $date->format('F');
        $dtcd = $date->format('d');
        $dtwn = $date->format('W');
        $dtdy = $date->format('N');
        $dtacs = NULL;
        $eddtt = NULL;
        $eddtt2 = NULL;
        //Genarate First and Last Date of Month
        $dtsdom = $date->format('Y-m-01'); //Firs date of month
        $dtldom = $date->format('Y-m-t'); //Last dte of month
        //Genarate Account Year Start Date
        if($dtcm>=4){
            $dtacs = $date->format('Y-04-01');
        }else{
            $eddtt = new DateTime( $dtdate );
            $eddtt2 = $eddtt->sub(new DateInterval('P12M'));
            $dtacs = $eddtt->format('Y-04-01');
        }

        //Set Last 10 Days
        $lstd=$this->dtdate;
        $crdt=new DateTime($lstd);
        $crdt->sub(new DateInterval('P10D'));
        $fstd=$crdt->format('Y-m-d');

        //Set Data
        $this->dtdate = $dtdate;
        $this->dtdatetime = $dtdatetime;
        $this->dtdatetimesp = $dtdatetimesp;
        $this->dtcyf = $dtcyf;
        $this->dtcy = $dtcy;
        $this->dtcm = $dtcm;
        $this->dtcmt = $dtcmt;
        $this->dtcd = $dtcd;
        $this->dtwn = $dtwn;
        $this->dtdy = $dtdy;
        $this->dtacs = $dtacs;
        $this->dtsdom = $dtsdom;
        $this->dtldom = $dtldom;
        $this->ftdate = $fstd;


    }


}

?>
