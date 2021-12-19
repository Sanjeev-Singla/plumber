<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\ApiBaseController;

class AuthController extends ApiBaseController
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
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required|min:3',
            'username'=>'required|unique:users,username'
        ]);

        if ($validator->fails()) {
            return $this->sendSingleFieldError($validator->errors()->first(),201,201);
        }
        $inputs = $request->all();
        $inputs['status'] = \Config::get('constant.users.status.enabled');
        $inputs['role'] = \Config::get('constant.users.role.admin');
        $user = User::create($inputs);
        
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
