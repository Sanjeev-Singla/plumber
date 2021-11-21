<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tool;
use Illuminate\Support\Facades\Auth;
use Validator;


class ToolController extends Controller
{

    /**
     * register
     *
     * @param  mixed $request
     * @return void
     */
     
    public function addTool(Request $request){
        
        if(!blank($request->all())){
            
            $admin = Auth::user();
		if( $admin['role'] == 0 || $admin['role'] == 1){
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'description' => 'required',
                    'manufacturer' => 'required',
                    'model' => 'required',
                    'serial_no' => 'required',
                    'barcode' => 'required',
                    'category' => 'required',
                    'avail' => 'required',
                    'loaned_to' => 'required',
                    'purchase_date' => 'required',
                    'price' => 'required',
                    'warranty_date' => 'required',
                    'web_url' => 'required',
                    'manual_url' => 'required',
                ]);
        
                if ($validator->fails()) {
                    return response()->json($validator->errors(), 201);
                }
                $inputs = $request->all();
                $vehicle = Tool::create($inputs);
    			
                return response()->json(['status' => 'Tool Added Successfully!'],  200);
                
            }else{
                return response()->json(['status' => 'No access!'],  401);
            }
        }else{
                $data= Array ( 
                                'name' => 
                                    array (
                                          'required' => 1 ,
                                          'type' => 'varchar'
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
                                'model' => 
                                    array (
                                          'required' => 1 ,
                                          'type' => 'varchar'
                                     ),
                                'serial_no' => 
                                    array (
                                          'required' => 1 ,
                                          'type' => 'int'
                                     ),
                                'barcode' => 
                                    array (
                                          'required' => 1 ,
                                          'type' => 'int'
                                     ),
                                'category' => 
                                    array (
                                          'required' => 1 ,
                                          'type' => 'varchar'
                                     ),
                                'avail' => 
                                    array (
                                          'required' => 1 ,
                                          'type' => 'varchar'
                                     ),
                                'loaned_to' => 
                                    array (
                                          'required' => 1 ,
                                          'type' => 'varchar'
                                     ),
                                'purchase_date' => 
                                    array (
                                          'required' => 1 ,
                                          'type' => 'YYYY/MM/DD'
                                     ),
                                'price' => 
                                    array (
                                          'required' => 1 ,
                                          'type' => 'int'
                                     ),
                                'web_url' => 
                                    array (
                                          'required' => 1 ,
                                          'type' => 'varchar'
                                     ),
                                 'manual_url' => 
                                    array (
                                          'required' => 1 ,
                                          'type' => 'varchar'
                                     ),
                                'warranty_date' => 
                                    array (
                                          'required' => 1 ,
                                          'type' => 'YYYY/MM/DD'
                                     ),
                                'status' => 
                                    array(
                                          'required' => 0,
                                          'type' => 'int',
                                          'comment' => '0=disable, 1=enable'
                                        )
                            );
                            
                return response()->json($data, 201);
            }
         
    }
    
    public function editTool(Request $request){
        
      if(!blank($request->all())){    
                $admin = Auth::user();
        
		if( $admin['role'] == 0 || $admin['role'] == 1){
                    
                $validator = Validator::make($request->all(), [
                    'id' => 'required',
                 
                ]);
        
                if ($validator->fails()) {
                    return response()->json($validator->errors(), 201);
                }
            
           

             $update = Tool::where('id',$request->id)->update($request->all());
            
            if ($update) {
                return response()->json(['success' => 'Tool updated successfully'], 200);
            }
            
              
                
            }else{
                return response()->json(['status' => 'No access!'],  401);
            }
    
         }else{
             
               $data= Array (   
                                'id' => 
                                   array (
                                          'required' => 1 ,
                                          'type' => 'int',
                                          'comment' => 'tool id is required'
                                      ),
                                 'name' => 
                                    array (
                                          'required' => 1 ,
                                          'type' => 'varchar'
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
                                'model' => 
                                    array (
                                          'required' => 1 ,
                                          'type' => 'varchar'
                                     ),
                                'serial_no' => 
                                    array (
                                          'required' => 1 ,
                                          'type' => 'int'
                                     ),
                                'barcode' => 
                                    array (
                                          'required' => 1 ,
                                          'type' => 'int'
                                     ),
                                'category' => 
                                    array (
                                          'required' => 1 ,
                                          'type' => 'varchar'
                                     ),
                                'avail' => 
                                    array (
                                          'required' => 1 ,
                                          'type' => 'varchar'
                                     ),
                                'loaned_to' => 
                                    array (
                                          'required' => 1 ,
                                          'type' => 'varchar'
                                     ),
                                'purchase_date' => 
                                    array (
                                          'required' => 1 ,
                                          'type' => 'YYYY/MM/DD'
                                     ),
                                'price' => 
                                    array (
                                          'required' => 1 ,
                                          'type' => 'int'
                                     ),
                                'web_url' => 
                                    array (
                                          'required' => 1 ,
                                          'type' => 'varchar'
                                     ),
                                 'manual_url' => 
                                    array (
                                          'required' => 1 ,
                                          'type' => 'varchar'
                                     ),
                                'warranty_date' => 
                                    array (
                                          'required' => 1 ,
                                          'type' => 'YYYY/MM/DD'
                                     ),
                                'status' => 
                                    array(
                                          'required' => 0,
                                          'type' => 'int',
                                          'comment' => '0=disable, 1=enable'
                                        )
                            );
                            
                return response()->json($data, 201);
             
         }    
        
    }
    
    public function deleteTool(Request $request){
        $admin = Auth::user();
		if( $admin['role'] == 0 || $admin['role'] == 1){
            $validator = Validator::make($request->all(), [
                'id' => 'required|numeric|exists:tools,id'
               
            ]);
    
            if ($validator->fails()) {
                return response()->json($validator->errors(), 201);
            }
            
            $delete = Tool::destroy($request->id);
            if($delete){
                return response()->json(['status' => 'Tool Deleted Successfully']);
            }else{
                return response()->json(['status' => 'Something Went Wrong']);
            }
        }else{
            return response()->json(['status' => 'No access!'],  401);
        }
        
    }
    
    public function allTool(){
        $admin = Auth::user();
		if( $admin['role'] == 0 || $admin['role'] == 1){
            $vehicle = Tool::all();
            return response()->json($vehicle, 200);
        }else{
            return response()->json(['status' => 'No access!'],  401);
        }
        
    }
    
    
    public function getTool(Request $request,$id){
        $admin = Auth::user();
		if( $admin['role'] == 0 || $admin['role'] == 1){
            $tool = Tool::where('id',$id);
            return response()->json($tool, 200);
        }else{
            return response()->json(['status' => 'No access!'],  401);
        }
    }
   
   
   
}
