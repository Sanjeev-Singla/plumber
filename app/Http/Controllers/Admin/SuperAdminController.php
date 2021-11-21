<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;
use App\Models\ProjectAssignedDetails;
use Illuminate\Support\Facades\Auth;
use Validator;


class SuperAdminController extends Controller
{
    public function addUser(Request $request){
        if(!blank($request->all())){
            $user = Auth::user();
            if($user['role'] == 0){
                
                $validator = Validator::make($request->all(), [
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'address' => 'required',
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
                    'address' => $request['address'],
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
                             )
                      );
                    
            return response()->json($data, 201);

        }
        
    }
}
