<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Http\Controllers\Admin\ApiBaseController;


class VehicleController extends ApiBaseController
{

    /**
     * register
     *
     * @param  mixed $request
     * @return void
     */
     
    public function addVehicle(Request $request){
        $admin = Auth::user();
        if( $admin['role'] == 0 || $admin['role'] == 1){
            $validator = Validator::make($request->all(), [
                'vehicle_no' => 'required',
                'description' => 'required',
                'manufacturer' => 'required',
                'model' => 'required',
                'type' => 'required',
                'km' => 'required',
                'alloted_user_id'=>"nullable|numeric|exists:users,id"
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 201);
            }
            
            $inputs = $request->all();
            Vehicle::create($inputs);
            
            return $this->sendResponse((object) [],'Vehicle Added Successfully!',200,200);
        }
        return $this->sendSingleFieldError(ACCESS_DENIED,401,401);
    }
        
    /**
     * editVehicle
     *
     * @param  mixed $request
     * @return void
     */
    public function editVehicle(Request $request){
        $admin = Auth::user();
		if( $admin['role'] == 0 || $admin['role'] == 1){
            
            $validator = Validator::make($request->all(), [
                            'id' => 'required|exists:vehicles,id',
                        ]);
            if ($validator->fails()) {
                return $this->sendSingleFieldError($validator->errors()->first(),201,201);
            }
            
            Vehicle::where('id',$request->id)->update($request->all());
            return $this->sendResponse((object) [],'Vehicle Updated Successfully',200,200);
            
        }
        return $this->sendSingleFieldError('No access!',401,401);
    }
        
    /**
     * deleteVehicle
     *
     * @param  mixed $request
     * @return void
     */
    public function deleteVehicle(Request $request){
        $admin = Auth::user();
		if( $admin['role'] == 0 || $admin['role'] == 1){
            $validator = Validator::make($request->all(), [
                'id' => 'required|numeric|exists:vehicles,id'
            ]);
    
            if ($validator->fails()) {
                return $this->sendSingleFieldError($validator->errors()->first(),201,201);
            }
            Vehicle::destroy($request->id);
            return $this->sendResponse((object) [],'Vehicle Deleted Successfully',200,200);
        }
        return $this->sendSingleFieldError(ACCESS_DENIED,401,401);
    }
        
    /**
     * allVehicle
     *
     * @return void
     */
    public function allVehicle(){
        $admin = Auth::user();
		if( $admin['role'] == 0 || $admin['role'] == 1){
            $vehicles = Vehicle::where('status',\Config::get('constant.vehicles.status.enabled'))
                        ->get();

            $vehicles->transform(function($vehicle){
                $vehicle->alloted_user = isset($vehicle->allotedUser)?$vehicle->allotedUser:(object) [];
                return $vehicle;
            });

            return $this->sendResponse($vehicles,'Vehicle List',200,200);
        }
        return $this->sendSingleFieldError('No access!',401,401);
    }
        
    /**
     * getVehicle , get single vehicle detaiils
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function getVehicle(Request $request,$id){
        $admin = Auth::user();
		if( $admin['role'] == 0 || $admin['role'] == 1){
            $vehicle = Vehicle::where('id',$id)->get();
            return $this->sendResponse($vehicle,'Vehicle Details',200,200);
        }
        return $this->sendSingleFieldError(ACCESS_DENIED,201,201);
    }
       
    /**
     * employeeList
     *
     * @param  mixed $request
     * @return void
     */
    public function employeeList(Request $request)
    {
        $admin = Auth::user();
		if( $admin['role'] == 0 || $admin['role'] == 1){
            $users = User::where('status',\Config::get('contant.users.status.enabled'))
                    ->where('role',\Config::get('contant.users.status.employee'))
                    ->select(['id','first_name','last_name'])
                    ->orderBy('first_name','ASC')
                    ->get();

            return $this->sendResponse($users,'Vehicle Details',200,200);
        }
        return $this->sendSingleFieldError(ACCESS_DENIED,201,201);
    }
}
