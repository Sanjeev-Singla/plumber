<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;
use App\Models\ProjectAssignedDetails;
use Illuminate\Support\Facades\Auth;
use Validator;


class ProjectController extends Controller
{

    /**
     * register
     *
     * @param  mixed $request
     * @return void
     */
    public function addProject(Request $request)
    {
    	if(!blank($request->all())){
                
            $admin = Auth::user();

    		if( $admin['role'] == 0 || $admin['role'] == 1){
                    $validator = Validator::make($request->all(), [
                        'project_name' => 'required',
                        'no_of_floors' => 'required',
                        'no_of_rooms' => 'required',
                        'location' => 'required'
                       // 'start_date' => 'required'
                    ]);
            
                    if ($validator->fails()) {
                        return response()->json($validator->errors(), 201);
                    }
                    
                    $inputs = $request->all();
                    
                    $project = Project::create($inputs);
                    
                    return response()->json(['status' => 'Added Successfully!'], 202);
            }else{
                return response()->json(['status' => 'No access!'],  401);
            }
        }else{
            $data= [
                'project_name' => 
                    array (
                            'required' => 1 ,
                            'type' => 'varchar'
                        ),
                'no_of_floors' => 
                    array (
                            'required' => 1 ,
                            'type' => 'int'
                        ),
                'no_of_rooms' => 
                    array (
                            'required' => 1 ,
                            'type' => 'int'
                        ),
                'location' => 
                    array (
                            'required' => 1 ,
                            'type' => 'varchar'
                        ),
                'start_date' => 
                    array (
                            'required' => 1 ,
                            'type' => 'YYYY/MM/DD'
                        ),
                'status' => [
                            'required' => 1 ,
                            'type' => 'int',
                            'comment' => '0=disable, 1=enable'
                        ]
            ];
            return response()->json($data, 201);
        }
    }
    
    
    public function updateProject(Request $request){
        
     $user = Auth::user();
    
    
     if( !blank($request->all())  ){
         
		if( $user['role'] == 0 || $user['role'] == 1){

                $validator = Validator::make($request->all(), [
                    'id' => 'required',
                ]);
        
                if ($validator->fails()) {
                    return response()->json($validator->errors(), 201);
                }
				
             $update = Project::where('id',$request->id)->update($request->all());
            
            if ($update) {
                return response()->json(['success' => 'updated successfully'], 200);
            }
            
                     
         }else{
              return response()->json(['status' => 'Only Admin can edit project Details!'],  401);
          }
         
     }else{
         
            $data= Array ( 
                            'id' => 
                            array (
                                  'required' => 1 ,
                                  'type' => 'int',
                                  'comment' => 'project id is required'
                             ),
                             
                            'project_name' => 
                                array (
                                      'required' => 1 ,
                                      'type' => 'varchar'
                                 ),
                            'no_of_floors' => 
                                array (
                                      'required' => 1 ,
                                      'type' => 'int'
                                 ),
                            'no_of_rooms' => 
                                array (
                                      'required' => 1 ,
                                      'type' => 'int'
                                 ),
                            'location' => 
                                array (
                                      'required' => 1 ,
                                      'type' => 'varchar'
                                 ),
                            'start_date' => 
                                array (
                                      'required' => 1 ,
                                      'type' => 'YYYY/MM/DD'
                                 ),
                           'status' => 
                                array (
                                      'required' => 1 ,
                                      'type' => 'int',
                                      'comment' => '0=disable, 1=enable'
                                 )     
                        
                        );
                return response()->json($data, 201);
                
          }     
          
        
    }
    
    public function assignProject(Request $request,$ID){
        $admin = Auth::user();
        if($admin['role'] == 1){
            $employee=  $request['assign_employees'];
            $usernames = explode(',', $employee);
            $employeeId = User::whereIn('username',$usernames)->pluck('id')->toArray();
            // $empIdsStr = implode(",",$employeeId);
            // $updateID = Project::where('id',$ID)->update(['assign_employees'=>$empIdsStr]);
            if(!blank($employee)){
            foreach($employeeId as $value){
                $assign = ProjectAssignedDetails::create([
                    'user_id' => $value,
                    'project_id' => $ID
                ]);
            }
                return response()->json(['status' => 'Assigned Successfully!'], 202);
            }else{
                $employeeId = User::whereIn('username',$usernames)->pluck('id')->toArray();
                $unassignedDate  = date('Y-m-d H:i:s');
                $updateID = ProjectAssignedDetails::where('project_id',$ID)->update(['unassigned_date'=>$unassignedDate]);
                return response()->json(['status' => 'Unassigned Successfully!'], 202);
            }
        }else{
            return response()->json(['status' => 'No access!'],  401);
        }
    }
    
    
    public function projectListing(){
        $admin = Auth::user();
		if( $admin['role'] == 0 || $admin['role'] == 1){
            $project = Project::all();
            return response()->json($project, 200);
        }else{
            return response()->json(['status' => 'No access!'],  401);
        }
    }
    
    public function getProject($id){
        $admin = Auth::user();
		if( $admin['role'] == 0 || $admin['role'] == 1){
            $project = Project::where('id',$id)->get();
            return response()->json($project, 200);
        }else{
            return response()->json(['status' => 'No access!'],  401);
        }
    }
    
    public function assignProjectListing(){
        $admin = Auth::user();
        
		if( $admin['role'] == 0 || $admin['role'] == 1){
            return response()->json($admin->users, 200);
        }else{
            return response()->json(['status' => 'No access!'],  401);
        }
    }
    
    public function deleteProject(Request $request){
        $admin = Auth::user();
		if( $admin['role'] == 0 || $admin['role'] == 1){
            $validator = Validator::make($request->all(), [
                'project_id' => 'required|numeric|exists:projects,id'
               
            ]);
    
            if ($validator->fails()) {
                return response()->json($validator->errors(), 201);
            }
            
            Project::destroy($request->project_id);
            return response()->json(['status' => 'Project Deleted Successfully']);
        }else{
            return response()->json(['status' => 'No access!'],  401);
        }
        
    }
    
    

   
   
}
