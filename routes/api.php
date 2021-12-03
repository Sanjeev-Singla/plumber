<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

/*Authentication for user*/
Route::group(['middleware'=>'auth:api'],function(){
    
	/*add admin*/
	Route::post('addUser', [Admin\AdminController::class, 'addUser']); 

    Route::post('change-password', [Admin\AdminController::class, 'updatePassword']);
    Route::get('logout', [Admin\AdminController::class, 'logout']);

    /*Project Routes*/
    Route::post('add-project',[Admin\ProjectController::class,'addProject']);
    Route::post('update-project',[Admin\ProjectController::class,'updateProject']);
    Route::get('single-project/{id}',[Admin\ProjectController::class,'getProject']);
    Route::post('assign-project/{id}',[Admin\ProjectController::class,'assignProject']);
    Route::get('list-projects',[Admin\ProjectController::class,'projectListing']);
    Route::post('delete-project',[Admin\ProjectController::class,'deleteProject']);
    Route::get('assigned-all-project',[Admin\ProjectController::class,'assignProjectListing']);



    /*Vehicle Routes*/
    Route::post('add-vehicle',[Admin\VehicleController::class,'addVehicle']);
    Route::post('update-vehicle',[Admin\VehicleController::class,'editVehicle']);
    Route::post('delete-vehicle',[Admin\VehicleController::class,'deleteVehicle']);	    
    Route::get('list-vehicles',[Admin\VehicleController::class,'allVehicle']);
    Route::get('single-vehicle/{id}',[Admin\VehicleController::class,'getVehicle']);

    
    /*Tool Routes*/
    Route::post('add-tool',[Admin\ToolController::class,'addTool']);
    Route::post('update-tool',[Admin\ToolController::class,'editTool']);
    Route::post('delete-tool',[Admin\ToolController::class,'deleteTool']);	    
    Route::get('list-tools',[Admin\ToolController::class,'allTool']);
    Route::get('single-tool/{id}',[Admin\ToolController::class,'getTool']);

    
    /*Employee Routes*/
    Route::post('add-employee', [Admin\EmployeeController::class, 'addEmployee']);
    Route::post('update-employee', [Admin\EmployeeController::class, 'updateEmployee']);
    Route::post('delete-employee', [Admin\EmployeeController::class, 'deleteEmployee']);
    Route::get('list-employees', [Admin\EmployeeController::class, 'allEmployee']);
    Route::get('single-employee/{id}', [Admin\EmployeeController::class, 'singleEmployee']);

    
     /*Safety Routes*/
    Route::post('add-safety',[Admin\SafetyController::class,'addSafety']);
    Route::post('update-safety',[Admin\SafetyController::class,'editSafety']);
    Route::post('delete-safety',[Admin\SafetyController::class,'deleteSafety']);	    
    Route::get('list-safety',[Admin\SafetyController::class,'allSafety']);
    Route::get('single-safety/{id}',[Admin\SafetyController::class,'getSafety']);

});