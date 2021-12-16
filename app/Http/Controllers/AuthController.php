<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * register
     *
     * @param  mixed $request
     * @return void
     */
    public function register(Request $request)
    {
        $validator = \Validator::make($request->all(), [
           'email' => 'required|unique:users,email',
            'password' => 'required|min:3'
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
            'status' => "Disable"  
        ]);
        if ($data = User::where('email', $email)->first()) {
             $id = $data['id'];
            $update['username'] =  $username ."_" . $id;
            User::where('email', $email)->update($update);
        }
        
        $user->token = $user->createToken('plumber')->accessToken;
        return response()->json($user, 200);
    }

    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $responseArray = [];
            $responseArray['token'] = $user->createToken('MyApp')->accessToken;
            $responseArray['name'] = $user->name;
            $responseArray['email'] = $user->email;
            $responseArray['first_name'] = $user->first_name;
            $responseArray['last_name'] = $user->last_name;
            $responseArray['address'] = $user->address;
            $responseArray['role'] = $user->role;
            return response()->json($responseArray, 202);
        } else {
            return response()->json(['error' => 'Unauthrized'], 202);
        }
    }
}
