<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Admin;
use App\Models\Post;
use App\Models\User;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\AdminLoginRequest;
use App\Http\Requests\UserDeleteRequest;
use App\Http\Requests\UserReadRequest;
use App\Http\Requests\PostDeleteRequest;
use App\Http\Requests\PostReadRequest;


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


    public function postRead(PostReadRequest $request) {
        
        $validated = $request->safe()->only(['id']);

        $status = 0;

        $data = Post::find($validated['id']);

        if($data) $status = 1;

        return response()->json([
            'data' => $data,
            'status' => $status
        ]);
    }


    public function postDelete(PostDeleteRequest $request) {
        $data = DB::table('posts')->where('id', $request->id)->delete();

        $status = 0;

        if($data) $status = 1;

        return response()->json([
            'status' => $status
        ]);
    }


    public function userRead(UserReadRequest $request) {
        
        $validated = $request->safe()->only(['id']);

        $status = 0;

        $data = User::find($validated['id']);

        if($data) $status = 1;

        return response()->json([
            'data' => $data,
            'status' => $status
        ]);
    }


    public function userDelete(UserDeleteRequest $request) {
        $data = DB::table('users')->where('id', $request->id)->delete();

        $status = 0;

        if($data) $status = 1;

        return response()->json([
            'status' => $status
        ]);
    }


    public function logout(){

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




    // public function logout(){
        
    //     auth()->user()->tokens()->delete();

    //    /* return a message that the user is logged out*/
    //    return response()->json([
        
    //         'message' => 'user logged out',
    //         'status' => 1
        
    //     ]);

    // }


