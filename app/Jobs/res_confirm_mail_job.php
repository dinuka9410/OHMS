<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Mail\confirmMailer;
use Illuminate\Support\Facades\Mail;

class res_confirm_mail_job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $holder;

    public function __construct($details)
    {

        $this->holder = $details;
  
    }

  
    public function handle()
    {

        Mail::to($this->holder['to'])->send(new confirmMailer($this->holder));

    }
}
