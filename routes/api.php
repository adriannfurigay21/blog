<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/user/update', [UserController::class, 'update']);
    Route::post('/user/list', [UserController::class, 'list']);
    Route::post('/user/delete', [UserController::class, 'delete']);
});


/* Admin Route */



/* User Route */
Route::post('/user/create', [UserController::class, 'create']);
Route::post('/user/read', [UserController::class, 'read']);
// Route::post('/user/update', [UserController::class, 'update']);
// Route::post('/user/list', [UserController::class, 'list']);
// Route::post('/user/delete', [UserController::class, 'delete']);

/* Post Route */
Route::post('/post/create', [PostController::class, 'create']);
Route::post('/post/read', [PostController::class, 'read']);
Route::post('/post/update', [PostController::class, 'update']);
Route::post('/post/list', [PostController::class, 'list']);
Route::post('/post/delete', [PostController::class, 'delete']);