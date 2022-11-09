<?php

namespace App\Http\Controllers;
// Importing the Post model
use App\Model\Post;

// Importing the class
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

// Importing from Request
use App\Http\Requests\PostCreateRequest;
use App\Http\Requests\PostReadRequest;
use App\Http\Requests\PostUpdateRequest;
use App\Http\Requests\PostDeleteRequest;
use App\Http\Requests\FormRequest;

class PostController extends Controller
{
    public function create(PostCreateRequest $request) {
        
        $validated = $request->safe()->all();

        $status = 0;

        if($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = Str::random(15) . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('public/Image'), $filename);
            $filename;
        }

        $validated['user_id'] = auth()->user()->id;

        $data = Product::create($validated);

        if($data) $status = 1;

        return response()->json([
            'data' => $data,
            'status' => $status
        ]);
    }


    public function read(PostReadRequest $request) {
        
        $status = 0; 

        // Get the Id to be view
        $data = Post::with(['user', 'post'])->whereId($request->id)->first();

        if($data) $status = 1;

        return response()->json([
            'data' => $data,
            'status' => $status
        ]);
    }


    public function update(PostUpdateRequest $request) {
        
        $status = 0;

        $validated = $request->safe()->all();

        $data = Post::find($validated['id']);

        $data->update($validated);

        if($data) $status = 1;

        return response()->json([
            'data' => $data,
            'status' => $status
        ]);
    }


    public function list(Request $request){
    
        $limit = ($request->limit) ?  $request->limit : 50;
        $sort_column = ( $request->sort_column) ?  $request->sort_column : 'id';
        $sort_order = ( $request->sort_order) ?  $request->sort_order : 'desc';
                 
        $data = new Wishlist;

        $data = Wishlist::with(['user','post'])->whereUserId(auth()->user()->id);

         /* Searching for the value of the request. */
         if(isset($request->search)) {

            $key = $request->search;

            /* Searching for the key in the columns. */
            $data = $data->whereHas('post', function ($q) use ($key) {

                /* Searching for the key in the column. */
                $q->where('name', 'LIKE', '%'.$key.'%')
                  ->orWhere('title', 'LIKE', '%'.$key.'%')
                  ->orWhere('tags', 'LIKE', '%'.$key.'%');        
            });
        }

        /* Filtering the data by date. */
        if($request->from && $request->to){
            
            $data = $data->whereBetween('created_at', [
                Carbon::parse($request->from)->format('Y-m-d H:i:s'), 
                Carbon::parse($request->to)->format('Y-m-d H:i:s')
            ]);
        }

        $data = $data->orderBy($sort_column, $sort_order)->paginate($limit);

        $status = 1;

        return response()->json([
            'data' => $data,
            'status' => $status
         ]);
    }


    public function delete(PostDeleteRequest $request) {
        
        $validated = $request->safe()->only(['id']);

        $status = 0;

        $data = Post::whereId($validated['id'])->delete();

        if($data) $status = 1;

        return response()->json([
            'status' => $status
        ]);
    }

}
