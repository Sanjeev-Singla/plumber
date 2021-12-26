<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Http\Controllers\Admin\ApiBaseController;

class EmployeeController extends ApiBaseController
{
    
    /**
     * __construct
     *
     * @return void
     */
    public function checkPermission()
    {
        $user = Auth::user();
		if( $user['role'] == 2 ){
            return $this->sendSingleFieldError(ACCESS_DENIED,401,401);
        }
    }

    /**
     * register
     *
     * @param  mixed $request
     * @return void
     */
     
    public function addEmployee(Request $request)
    {
        $this->checkPermission();
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|alpha',
            'last_name' => 'required|alpha',
            'address' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required',
            'username'  =>'required|unique:users,username'
        ]);
        if ($validator->fails()) {
            return $this->sendSingleFieldError($validator->errors()->first(),201,201);
        }

        $inputs = $request->all();
        $inputs['status'] = \Config::get('constant.users.status.enabled');
        $user = User::create($inputs);
        $user->token = $user->createToken('plumber')->accessToken;
        return $this->sendResponse($user, 'User added successfully.',200,200);
    }
        
    /**
     * updateEmployee
     *
     * @param  mixed $request
     * @return void
     */
    public function updateEmployee(Request $request)
    {
        $this->checkPermission();
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'first_name' => 'required|alpha',
            'last_name' => 'required|alpha',
            'email' => 'required|unique:users,email',
            'password' => 'required',
            'address' => 'required',
            'username'  =>'nullable|unique:users,username'
        ]);
        if ($validator->fails()) {
            return $this->sendSingleFieldError($validator->errors()->first(),201,201);
        }

        $userDetails =  User::find($request->id);
        $input = $request->all();

        if ($input['password']=="0000001") {
            unset($input['password']);
        }

        $userDetails->update($input);
        return $this->sendResponse((object) [], 'Employee Deleted Successfully',200,200);
    }
     
         
    /**
     * deleteEmployee
     *
     * @param  mixed $request
     * @return void
     */
    public function deleteEmployee(Request $request)
    {
        $this->checkPermission();
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric|exists:users,id'
        ]);

        if ($validator->fails()) {
            return $this->sendSingleFieldError($validator->errors()->first(),201,201);
        }
        User::update('status',\Config::get('constant.users.status.disabled'));
        return $this->sendResponse((object) [], 'User Deleted Successfully',200,200);
    }
        
    /**
     * allEmployee
     *
     * @return void
     */
    public function allEmployee()
    {
        $this->checkPermission();
        $users = User::where('status',\Config::get('constant.users.status.enabled'))
                ->where('role',\Config::get('constant.users.role.employee'))
                ->orderBy('first_name','ASC')
                ->get();
        return $this->sendResponse($users,'Employee list',200,200);
    }
    
    /**
     * singleEmployee
     *
     * @param  mixed $id
     * @return void
     */
    public function singleEmployee($id)
    {
        $this->checkPermission();
        $userDetails = User::where('id',$id)->get();
        return response()->json($userDetails, 200);
    }
}
