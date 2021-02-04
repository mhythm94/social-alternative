<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function login(Request $request)
    {
        // Retrieve user data from database and ensure that the user already exists
        $rules = array(
            'email' => 'required|email',
            'password' => 'required|min:4'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $validator->errors();
        } else {
            $user = User::where('email', $request->email)->get()->first();
            if (Hash::check($request->password, $user->password)) {
                // credentials correct
                return response()->json(['success' => true, $user], 200);
            } else {
                return response()->json(['success' => false, 'msg' => 'Password is incorrect'], 401);
            }
        }
    }

    public function register(Request $request)
    {
        // Create an account to a user
        $rules = array(
            'name' => 'required|min:3',
            'password' => 'required|min:4',
            'email' => 'required|email',
            'mobile' => 'required'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $validator->errors();
        } else {
            $token = Str::random(64);
            $user = User::create(
                [
                    'name' => $request->name,
                    'l_name' => $request->lName,
                    'mobile' => $request->mobile,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'token' => $token
                ]
            );
            if ($user == null) {
                return response()->json(['success' => false, 'msg' => 'Error occured'], 401);
            } else {
                return response()->json(['success' => true, 'user' => $user], 200);
            }
        }
    }
}
