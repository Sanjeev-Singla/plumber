<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;
use Validator;


class VehicleController extends Controller
{

    /**
     * register
     *
     * @param  mixed $request
     * @return void
     */
     
    public function addVehicle(Request $request){
        
        if(!blank($request->all())){
            
            $admin = Auth::user();
		if( $admin['role'] == 0 || $admin['role'] == 1){
                
                $validator = Validator::make($request->all(), [
                    'vehicle_no' => 'required',
                    'description' => 'required',
                    'manufacturer' => 'required',
                    'model' => 'required',
                    'type' => 'required',
                    'km' => 'required'
                   
                ]);
        
                if ($validator->fails()) {
                    return response()->json($validator->errors(), 201);
                }
                $inputs = $request->all();
                $vehicle = Vehicle::create($inputs);
    			
                return response()->json(['status' => 'Vehicle Added Successfully!'],  200);
                
            }else{
                return response()->json(['status' => 'No access!'],  401);
            }
        }else{
                $data= Array ( 
                                'vehicle_no' => 
                                    array (
                                          'required' => 1 ,
                                          'type' => 'int'
                                     ),
                                'description' => 
                                    array (
                                          'required' => 1 ,
                                          'type' => 'varchar'
                                     ),
                                'manufacturer' => 
                                    array (
                                          'required' => 1 ,
                                          'type' => 'varchar'
                                     ),
                                'km' => 
                                    array (
                                          'required' => 1 ,
                                          'type' => 'int'
                                     ),
                                'model' => 
                                    array (
                                          'required' => 1 ,
                                          'type' => 'varchar'
                                     ),
                                'type' => 
                                    array (
                                          'required' => 1 ,
                                          'type' => 'varchar'
                                     ),
                                'status' => 
                                    array(
                                          'required' => 1,
                                          'type' => 'int',
                                          'comment' => '0=disable, 1=enable'
                                         )
                            
                            );
            return response()->json($data, 201);
        }
         
    }
    
    public function editVehicle(Request $request){
        $admin = Auth::user();
		if( $admin['role'] == 0 || $admin['role'] == 1){
            
            $validator = Validator::make($request->all(), [
                            'id' => 'required',
            ]);
        
            if ($validator->fails()) {
                    return response()->json($validator->errors(), 201);
            }
                
            
            $update = Vehicle::where('id',$request->id)->update($request->all());
            
            return response()->json(['status' => 'Vehicle Updated Successfully!'], 200);
            
        }else{
            return response()->json(['status' => 'No access!'],  401);
        }
        
    }
    
    public function deleteVehicle(Request $request){
        $admin = Auth::user();
		if( $admin['role'] == 0 || $admin['role'] == 1){
            $validator = Validator::make($request->all(), [
                'id' => 'required|numeric|exists:vehicles,id'
               
            ]);
    
            if ($validator->fails()) {
                return response()->json($validator->errors(), 201);
            }
            $delete = Vehicle::destroy($request->id);
            if($delete){
                return response()->json(['status' => 'Vehicle Deleted Successfully']);
            }else{
                return response()->json(['status' => 'Something Went Wrong']);
            }
            return response()->json(['status' => 'No access!'],  401);
        }
        
    }
    
    public function allVehicle(){
        $admin = Auth::user();
		if( $admin['role'] == 0 || $admin['role'] == 1){
            $vehicle = Vehicle::all();
            return response()->json($vehicle, 200);
        }else{
            return response()->json(['status' => 'No access!'],  401);
        }
        
    }
    
    public function getVehicle(Request $request,$id){
        $admin = Auth::user();
		if( $admin['role'] == 0 || $admin['role'] == 1){
            $vehicle = Vehicle::where('id',$id);
            return response()->json($vehicle, 200);
        }else{
            return response()->json(['status' => 'No access!'],  401);
        }
    }
   
}
