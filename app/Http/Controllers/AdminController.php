<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function login(AdminLoginRequest $request)
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



    public function logout(AdminLogoutRequest $request){

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
}
