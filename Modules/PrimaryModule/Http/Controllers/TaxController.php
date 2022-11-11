<?php

namespace Modules\PrimaryModule\Http\Controllers;


use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Modules\PrimaryModule\Models\Agent;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use Yajra\DataTables\DataTables;
class TaxController extends Controller
{
    public function tax_type_define(Request $req)
    {

        // this is used in the top page navigation
        // ex : dashboard >> settings >> some other page
        // please provide page name and route name and key value pair in the
        // below associative array

        $params['pagenames'] = [
            [
                'displayname' => 'Agents',
                'routename' => 'tax_type_define'
            ],

        ];

        // this function will return agent view page

        return view('primarymodule::pages/tax_type_define', $params);
    }
}
