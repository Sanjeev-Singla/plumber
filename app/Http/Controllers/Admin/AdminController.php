<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use hash;
use App\Http\Controllers\Admin\ApiBaseController;


class AdminController extends ApiBaseController
{
    public function checkPermission()
    {
        $user = Auth::user();
		if( $user['role'] != 0 ){
            return $this->sendSingleFieldError(ACCESS_DENIED,401,401);
        }
    }
     /**
      * addUser
      *
      * @param  mixed $request
      * @return void
      */
     public function addUser(Request $request){
        $this->checkPermission();
        $validator = Validator::make($request->all(), [
            'first_name'=>  FIRST_NAME_VALIDATION,
            'last_name' =>  FIRST_NAME_VALIDATION,
            'username'  =>  'required|unique:users,username',
            'email'     =>  'required|unique:users,email',
            'password'  =>  'required',
            "status"    =>  'required'
        ]);
        if ($validator->fails()) {
            return $this->sendSingleFieldError($validator->errors()->first(),201,201);
        }

        $inputs = $request->all();
        $inputs['role'] = \Config::get('constant.users.role.admin');
        $user = User::create($inputs);

        $user->token = $user->createToken('plumber')->accessToken;
        return $this->sendResponse($user,'Password Updated Successfully!',200,200);
    }
	
        
    /**
     * updatePassword
     *
     * @param  mixed $request
     * @return void
     */
    public function updatePassword(Request $request) 
    {
        $this->checkPermission();
        $validator = Validator::make($request->all(),[ 
            'current_password' => 'required|different:new_password', 
            'new_password' => 'required|different:current_password', 
            'new_confirm_password' => 'required|same:new_password' 
        ]); 
        if ($validator->fails()) {
            return $this->sendSingleFieldError($validator->errors()->first(),201,201);
        }
        $user = \Auth::user();
        if (!\Hash::check($request->current_password, \Auth::user()->password)) { 
           return $this->sendSingleFieldError('You have entered the wrong current password',201,201);
        } 

        $user->update(['password' => $request->new_password]);
        return $this->sendResponse((object) [],'Password Updated Successfully!',200,200);
    }
    
        
    /**
     * logout
     *
     * @param  mixed $request
     * @return void
     */
    public function logout(Request $request) {
        
      Auth::logout();
      return response()->json(['status' => 'logout successfully'],200);
    }
          
    /**
     * deleteUser
     *
     * @param  mixed $request
     * @return void
     */
    public function deleteUser(Request $request)
    {
        $this->checkPermission();
        $validator = Validator::make($request->all(),[ 
            'user_id'   =>  'required|numeric|exists:users,id'
        ]); 
        if ($validator->fails()) {
            return $this->sendSingleFieldError($validator->errors()->first(),201,201);
        }
        
        User::whereId($request->user_id)
            ->update(['status'=>\Config::get('constant.users.status.disabled')]);
        return response()->json(['status' => 'User deleted successfully.'],200);
    }
    
    /**
     * userList
     *
     * @param  mixed $request
     * @return void
     */
    public function userList(Request $request)
    {
        $this->checkPermission();
        $validator = Validator::make($request->all(), [ 
            'page'   =>  'required|numeric'
        ]); 
        if ($validator->fails()) {
            return $this->sendSingleFieldError($validator->errors()->first(),201,201);
        }

        $perPage = 20;
        $skip = $request->page?20 * ($request->page - 1):0;

        $users = User::where('status',\Config::get('constant.users.status.enabled'))
                    ->where('role',\Config::get('constant.users.role.admin'))
                    ->orderBy('id','DESC')
                    ->take($perPage)
                    ->skip($skip)
                    ->get();
        return response()->json(['userList'=>$users,'status' => 'users list.'],200);
    }
    
    /**
     * checkUnique
     *
     * @param  mixed $request
     * @return void
     */
    public function checkUnique(Request $request)
    {
        $this->checkPermission();
        
        dd($request->all());
        $validator = Validator::make($request->all(), [
            $request->param  => 'unique:users,'.$request->param,
        ]);
        if ($validator->fails()) {
            return $this->sendSingleFieldError($validator->errors()->first(),201,201);
        }
        return $this->sendResponse((object) [],'Valid.',200,200);
    }
    
    /**
     * updateUser
     *
     * @param  mixed $request
     * @return void
     */
    public function updateUser(Request $request)
    {
        $this->checkPermission();
        $validator = Validator::make($request->all(), [
            'first_name'    => 'required|alpha',
            'last_name'     => 'required|alpha',
            'username'      => 'nullable|unique:users,username',
            'email'         => 'nullable|email',
            'user_id'       => 'required|numeric|exists:users,id',
            'password'      => 'required|min:6|max:255',
            'status'        => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendSingleFieldError($validator->errors()->first(),201,201);
        }
        $inputs = $request->all();
        unset($inputs['user_id']);
        \App\Models\User::where('id',$request->user_id)->update($inputs);
        return $this->sendResponse((object) [],'User updated successfully.',200,200);
    }
}