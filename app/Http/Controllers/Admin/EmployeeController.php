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
     * register
     *
     * @param  mixed $request
     * @return void
     */
     
    public function addEmployee(Request $request)
    {
        if(!blank($request->all())){
            $user = Auth::user();
		if( $user['role'] == 0 || $user['role'] == 1){
                
                $validator = Validator::make($request->all(), [
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'address' => 'required',
                    'email' => 'required|unique:users,email',
                    'password' => 'required'
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
                 return response()->json(['status' => 'Only Admin can add Employee!'],  401);
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
    
    public function updateEmployee(Request $request){
        
        $user = Auth::user();
        if(!blank($request->all())  ){
		if( $user['role'] == 0 || $user['role'] == 1){
              $validator = Validator::make($request->all(), [
                    'id' => 'required',
                ]);
    
            if ($validator->fails()) {
                return response()->json($validator->errors(), 201);
            }
            if($request['email']){
                 $validator = Validator::make($request->all(), [
                       'email' => 'required|unique:users,email',

                    ]);
        
                if ($validator->fails()) {
                    return response()->json($validator->errors(), 201);
                }
            }
            
                $email = $request['email'];
                $username = strstr($email,'@',true);
                
                
                $id = $request['id'];
                $user =  User::find($id);
               
                
                $newUsername =  $username ."_" . $id ;
                
                
                 $input = $request->all();
                 $input['username'] = $newUsername;
                $update = $user->update($input);
            
            if ($update) {
                return response()->json(['error' => 'Data updated Successfully'], 200);
            }
            
        }else{
              return response()->json(['status' => 'Only Admin can edit Employee Detail!'],  401);
        }
        
     }else{
            
             $data= Array (
                        'id' => 
                            array (
                                  'required' => 1 ,
                                  'type' => 'int',
                                  'comment' => 'user id is required'
                             ),
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
                           )
                      );
                    
            return response()->json($data, 201);

        }       
        
   }
     
     
    public function deleteEmployee(Request $request){
        $admin = Auth::user();
		if( $admin['role'] == 0 || $admin['role'] == 1){
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|numeric|exists:users,id'
               
            ]);
    
            if ($validator->fails()) {
                return response()->json($validator->errors(), 201);
            }
            
            User::destroy($request->user_id);
            return response()->json(['status' => 'User Deleted Successfully']);
        }else{
            return response()->json(['status' => 'No access!'],  401);
        }
        
    }
    
    public function allEmployee(){
        $user = Auth::user();
		if( $user['role'] == 0 || $user['role'] == 1){
            $user = User::where('status',\Config::get('constant.users.status.enabled'))
                    ->where('role',\Config::get('constant.users.role.employee'))
                    ->orderBy('first_name','ASC')
                    ->get();
            return $this->sendResponse($user,'Employee list',200,200);
        }
        return $this->sendSingleFieldError('No access!',401,401);
    }

    public function singleEmployee($id){
        $user = Auth::user();
		if( $user['role'] == 0 || $user['role'] == 1){
            $user = User::where('id',$id)->get();
            return response()->json($user, 200);
        }else{
            return response()->json(['status' => 'No access!'],  401);
        }

    }
     
    
}
