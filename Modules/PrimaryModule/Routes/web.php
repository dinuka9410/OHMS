<?php

use Illuminate\Support\Facades\Route;
use Modules\PrimaryModule\Http\Controllers\DashBoard_Controller;
use Modules\PrimaryModule\Http\Controllers\SeasonController;
use Modules\PrimaryModule\Http\Controllers\AgentController;
use Modules\PrimaryModule\Http\Controllers\MealPlanController;
use Modules\PrimaryModule\Http\Controllers\RoomRateController;
use Modules\PrimaryModule\Http\Controllers\Currncy_change;
use Modules\PrimaryModule\Http\Controllers\CurrencyController;
use Modules\PrimaryModule\Http\Controllers\Room_Controller;
use Modules\PrimaryModule\Http\Controllers\RoomBookingController;
use Modules\PrimaryModule\Http\Controllers\RoomReservationController;
use Modules\PrimaryModule\Http\Controllers\BillController;
use Modules\PrimaryModule\Http\Controllers\PageController;
use Modules\PrimaryModule\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Auth;
use Modules\PrimaryModule\Http\Controllers\ModuleController;
use Modules\PrimaryModule\Http\Controllers\PrimaryModuleController;
use Modules\PrimaryModule\Http\Controllers\UserController;
use Modules\PrimaryModule\Http\Controllers\TaxController;

/*

|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth', 'modulecheck:PrimaryModule', 'pagesetup'])->prefix('primarymodule')->group(function () {


    Route::get('dashboard', [DashBoard_Controller::class, 'index'])->name('dashboard');

    Route::get('/settings', [PageController::class, 'show_settings'])->name('settings')->middleware(['permissionCheck:1']);
    Route::post('/changelayout', [PageController::class, 'change_layout_theme'])->name('changelayout');

    // check if the unique field---------------------------------------------
    Route::get('checkifuniquefield', [PrimaryModuleController::class, 'checkifuniquefield'])->name('checkifuniquefield');


    // season related links
    Route::get('add_update_season_view', [SeasonController::class, 'add_update_season_view'])->name('add_update_season_view')->middleware(['permissionCheck:3']);
    Route::get('get_all_seasons', [SeasonController::class, 'getAllSeasons'])->name('get_all_seasons');
    Route::post('get_season_by_id', [SeasonController::class, 'get_season_by_id'])->name('get_season_by_id');
    Route::post('season_add_edit', [SeasonController::class, 'season_add_edit'])->name('season_add_edit')->middleware(['permissionCheck:3']);
    Route::post('delete_season', [SeasonController::class, 'delete_season'])->name('delete_season')->middleware(['permissionCheck:3']);

    // season form validation ------------------------------------------------
    Route::post('validate_season_code', [SeasonController::class, 'validate_season_code'])->name('validate_season_code');
    Route::post('validate_season_name', [SeasonController::class, 'validate_season_name'])->name('validate_season_name');
    Route::get('change_season_status', [SeasonController::class, 'change_season_status'])->name('change_season_status')->middleware(['permissionCheck:3']);

    // all agent related links
    Route::get('agents_view', [AgentController::class, 'agents_view'])->name('agents_view')->middleware(['permissionCheck:4']);
    Route::get('getagents', [AgentController::class, 'getAgents'])->name('getagents');
    Route::get('add_update_agent_view', [AgentController::class, 'add_update_agent_view'])->name('add_update_agent_view')->middleware(['permissionCheck:5']);
    Route::get('get_agent_by_id', [AgentController::class, 'get_agent_by_id'])->name('get_agent_by_id');
    Route::post('agent_add_edit', [AgentController::class, 'agent_add_edit'])->name('agent_add_edit');
    Route::post('agent_delete', [AgentController::class, 'deleteAgent'])->name('agent_delete')->middleware(['permissionCheck:5']);
    Route::get('change_agent_status', [AgentController::class, 'change_agent_status'])->name('change_agent_status')->middleware(['permissionCheck:5']);
    Route::post('validate_agent_code', [AgentController::class, 'validate_agent_code'])->name('validate_agent_code');
    Route::post('validate_agent_name', [AgentController::class, 'validate_agent_name'])->name('validate_agent_name');
    Route::post('validate_agent_email', [AgentController::class, 'validate_agent_email'])->name('validate_agent_email');

    // all meal plan related links
    Route::get('add_update_meal_view', [MealPlanController::class, 'add_update_meal_view'])->name('add_update_meal_view')->middleware(['permissionCheck:7']);
    Route::post('meal_add_edit', [MealPlanController::class, 'meal_add_edit'])->name('meal_add_edit')->middleware(['permissionCheck:7']);
    Route::post('delete_meal_plan', [MealPlanController::class, 'deleteMealPlan'])->name('delete_meal_plan')->middleware(['permissionCheck:7']);

    // room rates routes
    Route::get('agent_room_rate_view', [RoomRateController::class, 'agent_room_rate_view'])->name('agent_room_rate_view')->middleware(['permissionCheck:8']);
    Route::get('get_all_roomrate', [RoomRateController::class, 'get_all_roomrate'])->name('get_all_roomrate');
    Route::get('add_update_room_rate_view', [RoomRateController::class, 'add_update_room_rate_view'])->name('add_update_room_rate_view')->middleware(['permissionCheck:9']);
    Route::get('room_view_rate', [RoomRateController::class, 'room_view_rate'])->name('room_view_rate')->middleware(['permissionCheck:9']);
    Route::POST('agent_rates_add_edit', [RoomRateController::class, 'agent_rates_add_edit'])->name('agent_rates_add_edit');
    Route::post('checkcurrys_rate', [RoomRateController::class, 'checkcurrys_rate'])->name('checkcurrys_rate');
    Route::post('get_all_room_rate', [RoomRateController::class, 'get_all_room_rate'])->name('get_all_room_rate');
    Route::get('checkcurrys_rate_agent_room', [RoomRateController::class, 'checkcurrys_rate_agent_room'])->name('checkcurrys_rate_agent_room');
    Route::get('deleterate', [RoomRateController::class, 'deleterate'])->name('deleterate')->middleware(['permissionCheck:9']);

    // currency related routes
    Route::get('Currncy_change_get', [Currncy_change::class, 'Currncy_change_func'])->name('Currncy_change_get');
    Route::get('get_currencies', [CurrencyController::class, 'get_currencies'])->name('get_currencies');


    //room_catagory
    Route::get('room_category_view_add_update', [Room_Controller::class, 'View_Room_Catagory_Update_Edit'])->name('room_category_view_add_update')->middleware(['permissionCheck:12']);
    Route::post('Room_Category_Add_Update', [Room_Controller::class, 'Room_Category_Add_Update'])->name('Room_Category_Add_Update')->middleware(['permissionCheck:12']);
    Route::get('room_category_delete', [Room_Controller::class, 'room_category_delete'])->name('room_category_delete')->middleware(['permissionCheck:12']);
    Route::get('change_status_room_category', [Room_Controller::class, 'change_status_room_category'])->name('change_status_room_category');
    Route::post('img_uplord', [Room_Controller::class, 'Img_Uplord'])->name('img_uplord');
    Route::get('fetch_img', [Room_Controller::class, 'Fetch_Img'])->name('fetch_img');
    Route::post('edit_fetch_img', [Room_Controller::class, 'Edit_Fetch_Img'])->name('edit_fetch_img');
    Route::POST('remove_fetch_img', [Room_Controller::class, 'Remove_Fetch_Img'])->name('remove_fetch_img');
    Route::POST('edit_remove_fetch_img', [Room_Controller::class, 'Saved_Remove_Fetch_Img'])->name('edit_remove_fetch_img');

    // get the all rooms
    Route::get('get_all_rooms', [Room_Controller::class, 'get_all_rooms'])->name('get_all_rooms');

    //room_types routes
    Route::get('room_type_view', [Room_Controller::class, 'RoomTypeView'])->name('room_type_view')->middleware(['permissionCheck:13']);
    Route::get('room_type_add_edit', [Room_Controller::class, 'View_Room_Type_Update_Edit'])->name('room_type_add_edit')->middleware(['permissionCheck:13']);
    Route::get('room_type_delete', [Room_Controller::class, 'room_type_delete'])->name('room_type_delete')->middleware(['permissionCheck:13']);
    Route::post('add_update_room_type', [Room_Controller::class, 'Room_Type_Add_Update'])->name('add_update_room_type')->middleware(['permissionCheck:13']);
    Route::post('/delete_room_type', [Room_Controller::class, 'Delete_Room_Type'])->middleware(['permissionCheck:13']);
    Route::get('change_status_room_type', [Room_Controller::class, 'change_status_room_type'])->name('change_status_room_type')->middleware(['permissionCheck:13']);

    //room routes
    Route::get('room_view', [Room_Controller::class, 'RoomView'])->name('room_view')->middleware(['permissionCheck:14']);
    Route::get('room_add_edit', [Room_Controller::class, 'View_Room_ADD_Edit'])->name('room_add_edit')->middleware(['permissionCheck:15']);
    Route::get('room_view_add_edit', [Room_Controller::class, 'room_view_add_edit'])->name('room_view_add_edit')->middleware(['permissionCheck:15']);
    Route::post('room_add_update', [Room_Controller::class, 'Room_Add_Update'])->name('room_add_update')->middleware(['permissionCheck:15']);
    Route::get('getroomdeatils', [Room_Controller::class, 'getroomdeatils'])->name('getroomdeatils');
    Route::get('deleteRoom', [Room_Controller::class, 'deleteRoom'])->name('deleteRoom')->middleware(['permissionCheck:15']);
    Route::get('change_status', [Room_Controller::class, 'change_status'])->name('change_status')->middleware(['permissionCheck:15']);
    Route::get('room_view_ajax', [Room_Controller::class, 'room_view_ajax'])->name('room_view_ajax');

    // room facilities routes
    Route::post('add_additional_facilites', [Room_Controller::class, 'AddAdditionalFacilites'])->name('add_additional_facilites')->middleware(['permissionCheck:16']);
    Route::get('room_facilities_view', [Room_Controller::class, 'room_facilities_view'])->name('room_facilities_view')->middleware(['permissionCheck:16']);
    Route::get('get_all_room_facilities', [Room_Controller::class, 'getAllRoomFacilities'])->name('get_all_room_facilities');
    Route::get('get_facility', [Room_Controller::class, 'getFacility'])->name('get_facility');
    Route::post('add_update_facilities', [Room_Controller::class, 'add_update_facilities'])->name('add_update_facilities')->middleware(['permissionCheck:16']);
    Route::get('deletefacility', [Room_Controller::class, 'deleteFacility'])->name('deletefacility')->middleware(['permissionCheck:16']);
    Route::post('validate_facility_name', [Room_Controller::class, 'validate_facility_name'])->name('validate_facility_name');

    /* Notification */
    Route::post('clear_notification', [UserController::class, 'clear_notification'])->name('clear_notification');
});
