<?php

namespace Modules\PrimaryModule\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

use Exception;

class PrimaryModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('primarymodule::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('primarymodule::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('primarymodule::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('primarymodule::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }

    // this function will check if the provided field value is unique in the db table
    public function checkIfUniqueField(Request $req){

        try {
            
            $table = $req->table;
            $column = $req->column;
            $value = $req->value;

            $result = count(DB::select('select * from '.$table.' where '.$column.'="'.$value.'" '));

            $uniqueornot = false;

            if($result==0){

                $uniqueornot = true;

            }else{

                $uniqueornot = false;

            }

            $data = [
                'status'=>200,
                'error_status'=>0,
                'msg'=>'unique or not if checked as successful',
                'isunique'=>$uniqueornot,
            ];

            return response()->json($data);
            

        } catch (Exception $e) {
          
            $data = [
                'status'=>500,
                'error_status'=>1,
                'msg'=>'unable check for if unique value',
                'error_msg'=>$e->getMessage(),
                'isunique'=>false,
            ];

            return response()->json($data);

        }

    }

}
