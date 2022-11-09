<?php

namespace App\Http\Controllers;
// Importing the Post model
use App\Models\Post;

// Importing the class
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

// Importing from Request
use App\Http\Requests\PostCreateRequest;
use App\Http\Requests\PostReadRequest;
use App\Http\Requests\PostUpdateRequest;
use App\Http\Requests\PostDeleteRequest;
use App\Http\Requests\ListRequest;

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

        // $validated['user_id'] = user()->id;

        $data = Post::create($validated);

        if($data) $status = 1;

        return response()->json([
            'data' => $data,
            'status' => $status
        ]);
    }


    public function read(PostReadRequest $request) {
        
        $validated = $request->safe()->only(['id']);

        $status = 0;

        $data = Post::find($validated['id']);

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


    public function list(ListRequest $request){
    
        
        $search_columns  = ['title', 'tags'];

        $limit = ($request->limit) ?  $request->limit : 50;
        $sort_column = ( $request->sort_column) ?  $request->sort_column : 'id';
        $sort_order = ( $request->sort_order) ?  $request->sort_order : 'desc';
        
        $status = 0;
          
        $data = new Post;

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