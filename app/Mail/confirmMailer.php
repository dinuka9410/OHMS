<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;


class confirmMailer extends Mailable
{
    use Queueable, SerializesModels;


    // this function will send an email for the with reservation confirmation room bill attached
    // cannot use for other types of mail
 
    public function __construct($details)
    {

        $this->holder = $details;
 
    }

    public function build()
    {

        $pdf = $this->print_room_bill($this->holder['info'],'S');
        $subject = 'Reservation confirmation mail';
    
        return $this->subject($subject)
        ->view('mails.confirm_res_mail')
        ->attachData($pdf,'agreement.pdf',[
            'mime' => 'application/pdf',
        ]);
    }
}
