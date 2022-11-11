<?php

namespace Modules\PrimaryModule\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class Currncy_change extends Controller
{
    public function Currncy_change_func(Request $request)
    {
        $rate = $request->rate;
        $symbol = $request->symbol;
        Session::put('symbolC', $symbol);

        $request->session()->put('rateC',$rate );
        $request->session()->put('symbolC',$symbol );

        return;
    }
}
