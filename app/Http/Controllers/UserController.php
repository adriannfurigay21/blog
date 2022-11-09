<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

// Importing from Request
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserReadRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\UserDeleteRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\ListRequest;

class UserController extends Controller
{
    public function create(UserCreateRequest $request) {

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


    public function read(UserReadRequest $request) {
        
        $validated = $request->safe()->only(['id']);

        $status = 0;

        $data = User::find($validated['id']);

        if($data) $status = 1;

        return response()->json([
            'data' => $data,
            'status' => $status
        ]);
    }


    public function update(UserUpdateRequest $request) {

        //get all validated incoming request
        $validated = $request->safe()->all();
        
        //look for the user based on the id
        $data = User::find($validated['id']);
        
        if($data) $status = 1;
        
        try {

            $data->update($validated);

        } catch (\Throwable $th) {

            $status = 0;
        }

        return response()->json([
            'data' => $data,
            'status' => $status
        ]);
    }


    public function login(UserLoginRequest $request)
    {
        /* Getting the validated data from the request. */
        $validated = $request->safe()->only(['username', 'password']);

        /* Getting the first customer user with the username from the request. */
        $customer = User::where('username', $validated['username'])->first();

        /* Checking if the user exists, if the password is correct, if the pin is correct, if the status is active, if the status is blocked. */
        if ( !$customer || !Hash::check($validated['password'], $customer->password) ) {
               
            /* Returning a 401 status code with a message. */
            return response()->json([
                'message' => 'Invalid login details',
            ], 401);

        }

        /* Deleting all the tokens for the user. */
        $customer->tokens()->delete();

        /* Creating a token for the user. */
        $token = $customer->createToken('auth_token')->plainTextToken;

        /* Returning the token to the user. */
        return response()->json([
            'access_token'  => $token,
            'token_type'    => 'Bearer',
        ]);
    }  


    public function list(ListRequest $request){
        
        $search_columns  = ['username', 'first_name', 'last_name', 'email'];

        $limit = ($request->limit) ?  $request->limit : 50;
        $sort_column = ( $request->sort_column) ?  $request->sort_column : 'id';
        $sort_order = ( $request->sort_order) ?  $request->sort_order : 'desc';
        
        $status = 0;
          
        $data = new User;

        /* Searching for the value of the request. */
        if(isset($request->search)) {

            $key = $request->search;

            /* Searching for the key in the columns. */
            $data = $data->where(function ($q) use ($search_columns, $key) {

                foreach ($search_columns as $column) {

                    /* Searching for the key in the column. */
                    $q->orWhere($column, 'LIKE', '%'.$key.'%');
                }  
            });
        }
        
        /* Filtering the seller by status. */
        if (isset($request->status)) { 
            $data = $data->whereStatus($request->status);
        }

        /* Filtering the data by date. */
        if($request->from && $request->to) {
            
            $data = $data->whereBetween('created_at', [
                Carbon::parse($request->from)->format('Y-m-d H:i:s'), 
                Carbon::parse($request->to)->format('Y-m-d H:i:s')
            ]);
        }

        $data = $data->orderBy($sort_column, $sort_order)->paginate($limit);
        
        if($data) $status = 1;

        return response()->json([
            'data' => $data,
            'status' => $status
        ]);
    }


    public function logout(){
        
        auth()->user()->tokens()->delete();

       /* return a message that the user is logged out*/
       return response()->json([
        
            'message' => 'user logged out',
            'status' => 1
        
        ]);

    }


    public function delete(UserDeleteRequest $request) {
        
        $validated = $request->safe()->only(['id']);

        $status = 0;

        $data = User::whereId($validated)->delete();

        if($data) $status = 1;

        return response()->json([
            'status' => $status
        ]);
    }
}
