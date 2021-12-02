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
     public function addUser(Request $request){
		 
        if(!blank($request->all())){
            $user = Auth::user();
            if($user['role'] == 0){
                
                $validator = Validator::make($request->all(), [
                    'first_name' => 'required',
                    'last_name' => 'required',
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
            }else{
                 return response()->json(['status' => 'Only Super Admin can add Employee and Admin!'],  401);
            }
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
	
    
    public function updatePassword(Request $request) 
    {
        $this->validate($request,[ 
            'current_password' => 'required', 
            'new_password' => 'required|same:new_confirm_password', 
            'new_confirm_password' => 'required' 

        ]); 
        $data = $request->all();
        if (!\Hash::check($data['current_password'], auth()->user()->password)) 
        { 
           return response()->json(['status' => 'You have entered the wrong current password' ]);
        } else { 
            $user = User::find(Auth::id());
            $updatePasword = $user->update([ 'password' => $data['new_password'] ]);
            if ($updatePasword) { 
                    return response()->json(['status' => 'Password Updated Successfully!'],200);
                }else { 
                    return response()->json(['status' => 'Data not updated!']);
                }
           
        }
    }
    
    
    public function logout(Request $request) {
      Auth::logout();
      return response()->json(['status' => 'logout successfully']);
    }
      


}