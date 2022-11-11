<?php

namespace App\Http\Controllers;

// Models
use App\Models\Admin;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

// Request 
use App\Http\Requests\AdminLoginRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserCreateRequest;


class AuthenticationController extends Controller
{
    /* Admin Authentication */

    public function adminLogin(AdminLoginRequest $request)
    {   
        /* Getting the validated data from the request. */
        $validated = $request->safe()->only(['username', 'password']);

        /* Getting the first admin user with the username from the request. */
        $admin = Admin::where('username', $validated['username'])->first();

        /* Checking if the user exists, if the password is correct, if the pin is correct, and if the status is active. */
        if ( !$admin || !Hash::check($validated['password'], $admin->password) || $admin->status != 'active') {
            /* Returning a 401 status code with a message. */
            return response()->json([
                'message' => 'Invalid login details',
            ], 401);
        }

        /* Deleting all the tokens for the user. */
        $admin->tokens()->delete();

        /* Creating a token for the user. */
        $token = $admin->createToken('auth_token')->plainTextToken;

        /* Returning the token to the user. */
        return response()->json([
            'access_token'  => $token,
            'token_type'    => 'Bearer',
        ]);
    }


    public function adminLogout(){

        /* Set status to 0*/
        $status = 0;

        //check if admin is logged in
        if(Auth::guard('admin')->check()) {

            /*get the id of the admin*/
            $id = Auth::guard('admin')->id();
            //get the current admin logged in
            $admin = Admin::where('id', $id)->first();
            /* Deleting all the tokens for the user. */
            $admin->tokens()->delete();
            $status = 1;
        } else {
            return response()->json(['status' => $status]);
        }

        /* return a message that the admin is logged out*/
        return response()->json(['status' => $status]);
    }


    /* User Authentication */

    public function register(UserCreateRequest $request) {

        //get the validated request
        $validated = $request->safe()->all();

        $status = 0;

        $validated['password'] = Hash::make($validated['password']);

        //create the user with validated input
        $data = User::create($validated);

        $token = $data->createToken('myapptoken')->plainTextToken;

        if($data) $status = 1;

        return response()->json([
            "status" => $status,
            "data" => $data,
            "token" => $token
        ]);
    }

    public function userLogin(UserLoginRequest $request)
    {
        /* Getting the validated data from the request. */
        $validated = $request->safe()->only(['username', 'password']);

        /* Getting the first customer user with the username from the request. */
        $user = User::where('username', $validated['username'])->first();

        /* Checking if the user exists, if the password is correct, if the pin is correct, if the status is active, if the status is blocked. */
        if ( !$user || !Hash::check($validated['password'], $user->password) ) {
               
            /* Returning a 401 status code with a message. */
            return response()->json([
                'message' => 'Invalid login details',
            ], 401);

        }

        /* Deleting all the tokens for the user. */
        $user->tokens()->delete();

        /* Creating a token for the user. */
        $token = $user->createToken('auth_token')->plainTextToken;

        /* Returning the token to the user. */
        return response()->json([
            'access_token'  => $token,
            'token_type'    => 'Bearer',
        ]);
    }  


    public function userLogout(){
        
        auth()->user()->tokens()->delete();

       /* return a message that the user is logged out*/
       return response()->json([
        
            'message' => 'user logged out',
            'status' => 1
        
        ]);

    }
}
