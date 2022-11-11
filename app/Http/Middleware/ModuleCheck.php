<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\PrimaryModule\Models\Cfg_module;
use Nwidart\Modules\Facades\Module;

class ModuleCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $module_name)
    {
        $Cfg_module = new Cfg_module();

        if($Cfg_module->ModuleExists($module_name) && Module::has($module_name)){
            return $next($request);

         }else{

             $data = [
                 'status'=>'400',
                 'error_status'=>'3',
                 'msg'=>'No Perimissions'
             ];
             return redirect()->route('dashboard')->with('status',$data);

         }
    }
}
