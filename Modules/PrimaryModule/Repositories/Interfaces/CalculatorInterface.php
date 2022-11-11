<?php

namespace Modules\PrimaryModule\Repositories\Interfaces;


interface CalculatorInterface{

    public function get_reservation_total_bill($res_id,$discount);
    public function calculate_daily_forecast($checkinday);
    public function calculate_room_forecast($fromdate,$todate);
    public function pdf_room_forecast($fromdate,$todate);

}


?>