<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserAuthController extends Controller
{
    public function register(Request $request){
        $registerUserData = $request->validate([
            'name'=>'required|string',
            'email'=>'required|string|email|unique:users',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
        $user = User::create([
            'name' => $registerUserData['name'],
            'email' => $registerUserData['email'],
            'password' => Hash::make($registerUserData['password']),
        ]);
        $success['token'] =  $user->createToken('MyApp')->plainTextToken;
        $success['name'] =  $user->name;
        return response()->json([
            'message' => 'User Created ',
            'token' => $success['token'],
            'name' => $success['name'],
        ]);
    }
    public function login(Request $request){
        $loginUserData = $request->validate([
            'email'=>'required|string|email',
            'password'=>'required|min:8'
        ]);
        $user = User::where('email',$loginUserData['email'])->first();
        if(!$user || !Hash::check($loginUserData['password'],$user->password)){
            return response()->json([
                'message' => 'Invalid Credentials'
            ],401);
        }
        $token = $user->createToken('MyApp')->plainTextToken;
        return response()->json([
            'access_token' => $token,
              'message' => 'Logined Successfully',
        ]);
    }
    public function logout(){
        auth()->user()->tokens()->delete();

        return response()->json([
          "message"=>"logged out"
        ]);
    }
}
