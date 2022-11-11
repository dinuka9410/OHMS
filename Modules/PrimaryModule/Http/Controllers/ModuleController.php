<?php

namespace Modules\PrimaryModule\Http\Controllers;

use Modules\PrimaryModule\Models\Cfg_module;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Nwidart\Modules\Facades\Module;
use ZanySoft\Zip\Zip;

class ModuleController extends Controller
{
    public function module_update()
    {
        $data['pagenames'] = [
            [
                'displayname' => 'Modules Update',
                'routename' => 'module'
            ],

        ];
        $data['module_list'] = Cfg_module::get();
        return view('primarymodule::pages.module_update')->with($data);
    }

    public function module_save(Request $request)
    {
        try {
            $modules_ids = explode(",", $request->modules_array);
            $cfg_module = Cfg_module::get();

            $failed_modules = [];

            foreach ($cfg_module as $module) {
                if ($module->id == '1') {
                    continue;
                }

                //active module
                if (array_search($module->id, $modules_ids) > 0) {

                    $zip_path = '../Modules/' . $module->module_name . '.zip';

                    //unzip module
                    if (!$this->module_file_check($module->module_name)) {
                        if (Zip::check($zip_path)) {
                            $zip = Zip::open($zip_path);
                            $zip->extract('../Modules');
                            //Module::update($module->module_name);
                            //Module::register();
                            //Module::allEnabled();
                            //Module::boot();
                            $module_tmp = Module::find($module->module_name);
                            $module_tmp->enable();
                            $module_tmp->getRequires();

                            Artisan::call('module:migrate-refresh ' . $module->module_name);
                            Artisan::call('module:seed ' . $module->module_name);

                            $module->status = 1;
                            $module->files_status = 1;
                            $module->last_update_time = $this->dtdatetime;
                            $module->save();
                        } else {

                            //unavilabal module zip
                            $module->status = 0;
                            $module->files_status = 0;
                            $module->last_update_time = $this->dtdatetime;
                            $module->save();
                        }
                    }
                } else if ($this->module_file_check($module->module_name)) { //inactive module and delete exist files
                    $module_tmp = Module::find($module->module_name);
                    $module_tmp->delete();

                    $module->status = 0;
                    $module->files_status = 0;
                    $module->last_update_time = $this->dtdatetime;
                    $module->save();
                } else { //inactive module
                    $module->status = 0;
                    $module->files_status = 0;
                    $module->last_update_time = $this->dtdatetime;
                    $module->save();
                }
            }
            $data = [
                'status' => '200',
                'error_status' => '0',
                'msg' => 'Module updated successfully'
            ];

            return redirect('primarymodule/dashboard')->with('status', $data);
        } catch (\Throwable $th) {
            $data = [
                'status' => '400',
                'error_status' => '1',
                'msg' => 'Module update failed'
            ];

            return redirect()->back()->with('status', $data);
        }
    }

    public function module_file_check($module_name)
    {
        return Module::has($module_name);
    }
}
