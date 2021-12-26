<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Safety;
use Illuminate\Support\Facades\Auth;
use Validator;


class SafetyController extends Controller
{

    /**
     * register
     *
     * @param  mixed $request
     * @return void
     */
     
    public function addSafety(Request $request){
        
       if(!blank($request->all())){
           
            $admin = Auth::user();
		if( $admin['role'] == 0 || $admin['role'] == 1){
                $validator = Validator::make($request->all(), [
                    'title' => 'required',
                    'description' => 'required',
                    'date' => 'required'
                ]);
        
                if ($validator->fails()) {
                    return response()->json($validator->errors(), 201);
                }
                $inputs = $request->all();
                $safety = Safety::create($inputs);
    			
                return response()->json(['status' => 'Safety Added Successfully!'],  200);
                
            }else{
                return response()->json(['status' => 'No access!'],  401);
            }
        }else{
            $data= Array ( 
                            'title' => 
                                array (
                                      'Required' => 1 ,
                                      'Type' => 'varchar'
                                 ),
                            'description' => 
                                array (
                                      'Required' => 1 ,
                                      'Type' => 'varchar'
                                 ),
                            'date' => 
                                array (
                                      'Required' => 1 ,
                                      'Type' => 'YYYY/MM/DD'
                                 )
                        
                        );
            return response()->json($data, 201);
        }
         
    }
    
    public function editSafety(Request $request){
        $admin = Auth::user();
        
    if( !blank($request->all())  ){    
		if( $admin['role'] == 0 || $admin['role'] == 1){
            
            
           if( !isset($request['id'] ) && empty( $request['id']) ){
              
                  return response()->json( ['status' => 'not a valid request', 'id' => 'safety id is required'],  401 );
            
                
           }elseif( isset($request['id'] ) && isset($request['action']) && $request['action'] == 'view'){
                    
                    $id = $request['id'] ;
                    $safety =  Safety::find($id);
                    
                    return $safety ;
            }
            
            $id      = $request['id'];
            $safety =  Safety::find($id);
            
            $inputs = $request->all();
             
            $safety->update($inputs);
            
            return response()->json(['status' => 'Safety Updated Successfully!'], 200);
            
        }else{
            return response()->json(['status' => 'No access!'],  401);
        }
        
    }else{
        
         $data= Array (     
                         'id' => 
                            array (
                                  'required' => 1 ,
                                  'type' => 'int',
                                  'comment' => 'safety id is required'
                             ),
                            'title' => 
                                array (
                                      'required' => 1 ,
                                      'type' => 'varchar'
                                 ),
                            'description' => 
                                array (
                                      'required' => 1 ,
                                      'type' => 'varchar'
                                 ),
                            'date' => 
                                array (
                                      'required' => 1 ,
                                      'type' => 'YYYY/MM/DD'
                                 )
                        
                        );
            return response()->json($data, 201);
    }    
        
    }
    
    public function deleteSafety(Request $request){
        $admin = Auth::user();
		if( $admin['role'] == 0 || $admin['role'] == 1){
            $validator = Validator::make($request->all(), [
                'safety_id' => 'required|numeric|exists:safeties,id'
               
            ]);
    
            if ($validator->fails()) {
                return response()->json($validator->errors(), 201);
            }
            
            Safety::destroy($request->safety_id);
            return response()->json(['status' => 'Safety Deleted Successfully']);
        }else{
            return response()->json(['status' => 'No access!'],  401);
        }
        
    }
    
    public function allSafety(Request $request){
        $admin = Auth::user();
		if( $admin['role'] == 0 || $admin['role'] == 1){

            $vehicle = Safety::orderBy('id','DESC');
            if (!blank($request->name) && !blank($request->date_from) &&  !blank($request->date_to)) {
                $vehicle = $vehicle->where('title',$request->name)
                            ->whereDate('created_at','>=',$request->date_from)
                            ->whereDate('created_at','<=',$request->date_to);
            }elseif(!blank($request->date_from) && !blank($request->date_to)){
                $vehicle = $vehicle->whereDate('created_at','>=',$request->date_from)
                            ->whereDate('created_at','<=',$request->date_to);
            }elseif(!blank($request->name)){
                $vehicle = $vehicle->where('title',$request->name);
            }
            $vehicle = $vehicle->get();
            return $this->sendResponse($vehicle, 'Safety list.',200,200);
        }
        return $this->sendSingleFieldError('No access!',401,401);
    }
    
    public function getSafety($id){
        $admin = Auth::user();
		if( $admin['role'] == 0 || $admin['role'] == 1){
            $safety = Safety::where('id',$id)->get();
            return response()->json($safety, 200);
        }else{
            return response()->json(['status' => 'No access!'],  401);
        }
    }
    
   
   
   
}
