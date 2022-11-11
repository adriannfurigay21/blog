<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Post;
use App\Models\User;

use Illuminate\Support\Facades\DB;
use App\Http\Requests\UserDeleteRequest;
use App\Http\Requests\UserReadRequest;
use App\Http\Requests\PostDeleteRequest;
use App\Http\Requests\PostReadRequest;


class AdminController extends Controller
{
   
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
}


