<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;
use App\Models\ProjectAssignedDetails;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Http\Controllers\Admin\ApiBaseController;


class ProjectController extends ApiBaseController
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
                    
                    Project::create($inputs);
                    
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
                    return $this->sendSingleFieldError($validator->errors()->first(),201,201);
                }
                Project::where('id',$request->id)->update($request->all());
                
                return response()->json(['success' => 'updated successfully'], 200);
            }
            return $this->sendSingleFieldError('No access!',401,401);
            
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
            if(!blank($employee)){
            foreach($employeeId as $value){
                ProjectAssignedDetails::create([
                    'user_id' => $value,
                    'project_id' => $ID
                ]);
            }
                return response()->json(['status' => 'Assigned Successfully!'], 202);
            }else{
                $unassignedDate  = date('Y-m-d H:i:s');
                ProjectAssignedDetails::where('project_id',$ID)->update(['unassigned_date'=>$unassignedDate]);
                return response()->json(['status' => 'Unassigned Successfully!'], 202);
            }
        }
        return $this->sendSingleFieldError('No access!',401,401);
    }
    
    
    public function projectListing(){
        $admin = Auth::user();
		if( $admin['role'] == 0 || $admin['role'] == 1){
            $projects = Project::all();

            $projects->transform(function($project){
                $project->assigned_users = $project->users()->get(['users.id','users.first_name','users.last_name']);
                return $project;
            });

            return $this->sendResponse($projects,'Projects List',200,200);
        }
        return $this->sendSingleFieldError('No access!',401,401);
    }
    
    public function getProject($id){
        $admin = Auth::user();
		if( $admin['role'] == 0 || $admin['role'] == 1){
            $project = Project::where('id',$id)->get();

            return $this->sendResponse($project,'Projects Details',200,200);
        }
        return $this->sendSingleFieldError('No access!',401,401);
    }
    
    public function assignProjectListing(){
        $admin = Auth::user();
        
		if( $admin['role'] == 0 || $admin['role'] == 1){
            return $this->sendResponse($admin->users,'Assigned project list',200,200);
        }
        return $this->sendSingleFieldError('No access!',401,401);
    }
    
    public function deleteProject(Request $request){
        $admin = Auth::user();
		if( $admin['role'] == 0 || $admin['role'] == 1){
            $validator = Validator::make($request->all(), [
                'project_id' => 'required|numeric|exists:projects,id'
               
            ]);
            if ($validator->fails()) {
                return $this->sendSingleFieldError($validator->errors()->first(),201,201);
            }
            
            Project::destroy($request->project_id);
            return $this->sendResponse((object) [],'Project Deleted Successfully',200,200);
        }
        return $this->sendSingleFieldError('No access!',401,401);
    }
   
}
