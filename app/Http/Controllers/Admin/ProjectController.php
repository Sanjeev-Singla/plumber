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
            return response()->json(['status' => ACCESS_DENIED],  401);
        }
    }
    
        
    /**
     * updateProject
     *
     * @param  mixed $request
     * @return void
     */
    public function updateProject(Request $request)
    {
        $user = Auth::user();
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
        return $this->sendSingleFieldError(ACCESS_DENIED,401,401);
    }
        
    /**
     * assignProject
     *
     * @param  mixed $request
     * @param  mixed $ID
     * @return void
     */
    public function assignProject(Request $request,$ID)
    {
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
        return $this->sendSingleFieldError(ACCESS_DENIED,401,401);
    }
    
        
    /**
     * projectListing
     *
     * @return void
     */
    public function projectListing()
    {
        $admin = Auth::user();
		if( $admin['role'] == 0 || $admin['role'] == 1){
            $projects = Project::all();

            $projects->transform(function($project){
                $project->assigned_users = $project->users()->get(['users.id','users.first_name','users.last_name']);
                return $project;
            });

            return $this->sendResponse($projects,'Projects List',200,200);
        }
        return $this->sendSingleFieldError(ACCESS_DENIED,401,401);
    }
        
    /**
     * getProject
     *
     * @param  mixed $id
     * @return void
     */
    public function getProject($id)
    {
        $admin = Auth::user();
		if( $admin['role'] == 0 || $admin['role'] == 1){
            $project = Project::where('id',$id)->get();

            return $this->sendResponse($project,'Projects Details',200,200);
        }
        return $this->sendSingleFieldError('No access!',401,401);
    }
        
    /**
     * assignProjectListing
     *
     * @return void
     */
    public function assignProjectListing()
    {
        $admin = Auth::user();
        
		if( $admin['role'] == 0 || $admin['role'] == 1){
            return $this->sendResponse($admin->users,'Assigned project list',200,200);
        }
        return $this->sendSingleFieldError(ACCESS_DENIED,401,401);
    }
        
    /**
     * deleteProject
     *
     * @param  mixed $request
     * @return void
     */
    public function deleteProject(Request $request)
    {
        $admin = Auth::user();
		if( $admin['role'] == 0 || $admin['role'] == 1){
            $validator = Validator::make($request->all(), [
                'project_id' => 'required|numeric|exists:projects,id'
               
            ]);
            if ($validator->fails()) {
                return $this->sendSingleFieldError($validator->errors()->first(),201,201);
            }
            Project::update('status',\Config::get('constant.projects.status.disabled'));
            return $this->sendResponse((object) [],'Project Deleted Successfully',200,200);
        }
        return $this->sendSingleFieldError(ACCESS_DENIED,401,401);
    }
   
}
