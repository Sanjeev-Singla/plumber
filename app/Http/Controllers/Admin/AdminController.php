<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use hash;


class AdminController extends Controller
{
         
     /**
      * addUser
      *
      * @param  mixed $request
      * @return void
      */
     public function addUser(Request $request){
		 
        if(!blank($request->all())){
            $user = Auth::user();
            if($user['role'] == 0){
                
                $validator = Validator::make($request->all(), [
                    'first_name' => 'required|alpha',
                    'last_name' => 'required|alpha',
					'email' => 'required|unique:users,email',
                    'password' => 'required',
                    'role' => 'required'
                ]);
        
                if ($validator->fails()) {
                    return response()->json($validator->errors(), 201);
                }
                $email = $request['email'];
                $username = strstr($email,'@',true);
                $user = User::create([
                    'username'=> $username,
                    'first_name' => $request['first_name'],
                    'last_name' => $request['last_name'],
                    'email' => $request['email'],
                    "password"=> $request['password'],
                    "role"=>  $request['role'],
                    'status' => 1  
                ]);
                if ($data = User::where('email', $email)->first()) {
                     $id = $data['id'];
                    $update['username'] =  $username ."_" . $id;
                    User::where('email', $email)->update($update);
                }
                $user->token = $user->createToken('plumber')->accessToken;
                return response()->json($user, 200);
            }
            return response()->json(['status' => 'Only Super Admin can add Employee and Admin!'],  401);
        }else{
            
             $data= Array (
                        'username' => 
                            array (
                                  'required' => 1 ,
                                  'type' => 'var'
                             ),
                        'first_name' => 
                            array (
                                  'required' => 1 ,
                                  'type' => 'var'
                             ),
                        'last_name' => 
                            array (
                                  'required' => 1 ,
                                  'type' => 'var'
                             ),
                        'address' => 
                            array (
                                  'required' => 1 ,
                                  'type' => 'var'
                             ),
                        'email' => 
                            array (
                                  'required' => 1 ,
                                  'type' => 'var'
                             ),
                        'password' => 
                            array (
                                  'required' => 1 ,
                                  'type' => 'var'
                             ),
						 'status' => 
                            array (
                                  'required' => 1 ,
                                  'type' => 'int',
                                  'comment' => '0=disable, 1=enable'
                           ),
						'role' => 
                            array (
                                  'required' => 1 ,
                                  'type' => 'int',
								  'comment' => 'super-admin=0, admin=1, employee=2'
                             )
                      );
                    
            return response()->json($data, 201);

        }
        
    }
	
        
    /**
     * updatePassword
     *
     * @param  mixed $request
     * @return void
     */
    public function updatePassword(Request $request) 
    {
        $validator = Validator::make($request->all(),[ 
            'current_password' => 'required|different:new_password', 
            'new_password' => 'required|different:current_password', 
            'new_confirm_password' => 'required|same:new_password' 
        ]); 
        if ($validator->fails()) {
            return response()->json($validator->errors(), 201);
        }
        $data = $request->all();
        if (!\Hash::check($data['current_password'], auth()->user()->password)) { 

           return response()->json(['status' => 'You have entered the wrong current password' ],201);

        } else { 

            $user = User::find(Auth::id());
            $updatePasword = $user->update([ 'password' => $data['new_password'] ]);
            if ($updatePasword) { 
                return response()->json(['status' => 'Password Updated Successfully!'],200);
            }else { 
                return response()->json(['status' => 'Data not updated!'],201);
            }

        }
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
        if (\Auth::user()->role == 0) {
            $validator = Validator::make($request->all(),[ 
                'user_id'   =>  'required|numeric|exists:users,id'
            ]); 
            if ($validator->fails()) {
                return response()->json($validator->errors(), 201);
            }
            
            \App\Models\User::where('id',$request->user_id)->update(['status'=>\Config::get('constant.users.status.disabled')]);
            return response()->json(['status' => 'User deleted successfully.'],200);
        }
        return response()->json(['status' => 'Not authorized to perform.'],201);
    }
    
    /**
     * userList
     *
     * @param  mixed $request
     * @return void
     */
    public function userList(Request $request)
    {
        if (\Auth::user()->role == 0) {
            $validator = Validator::make($request->all(), [ 
                'page'   =>  'required|numeric'
            ]); 
            if ($validator->fails()) {
                return response()->json($validator->errors(), 201);
            }

            $perPage = 20;
            $skip = $request->page?20 * ($request->page - 1):0;

            $users = \App\Models\User::where('status',\Config::get('constant.users.status.enabled'))
                                        ->where('role',\Config::get('constant.users.role.admin'))
                                        ->orderBy('id','DESC')
                                        ->take($perPage)
                                        ->skip($skip)
                                        ->get();
            return response()->json(['userList'=>$users,'status' => 'users list.'],200);
        }
        return response()->json(['status' => 'Not authorized to perform.'],201);
    }
    
    /**
     * updateUser
     *
     * @param  mixed $request
     * @return void
     */
    public function updateUser(Request $request)
    {
        if(\Auth::user()->role == 0){
            $validator = Validator::make($request->all(), [
                'first_name'    => 'required|alpha',
                'last_name'     => 'required|alpha',
                'email'         => 'required|unique:users,email',
                'role'          => 'required',
                'user_id'       => 'required|numeric|exists:users,id'
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 201);
            }
            $inputs = $request->all();
            unset($inputs['user_id']);
            \App\Models\User::where('id',$request->user_id)->update($inputs);
            return response()->json(['status' => 'User updated successfully.'],200);
        }
        return response()->json(['status' => 'Not authorized to perform.'],201);
    }
}