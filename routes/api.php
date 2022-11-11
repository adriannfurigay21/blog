<?php

//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthenticationController;


/* Public Routes */
Route::group(['middleware' => ['guest']], function () {
    Route::post('/user/login', [AuthenticationController::class, 'userLogin']);
    Route::post('/user/register', [AuthenticationController::class, 'register']);
    Route::post('/user/read', [UserController::class, 'read']);
    Route::post('/admin/login', [AuthenticationController::class, 'adminLogin']);
});

/* Protected Routes */

// Admin Route
Route::group(['prefix' => 'admin', 'middleware' => ['auth:admin', 'admin']], function() {

    Route::post('/logout', [AuthenticationController::class, 'adminLogout']);

    Route::group(['prefix' => 'user'], function() {
        Route::post('/read', [AdminController::class, 'userRead']);
        Route::post('/delete', [AdminController::class, 'userDelete']); 
    });

    Route::group(['prefix' => 'post'], function() {
        Route::post('/read', [AdminController::class, 'postRead']);
        Route::post('/delete', [AdminController::class, 'postDelete']);       
    });  
});

// User Route
Route::group(['prefix' => 'user', 'middleware' => ['auth:user', 'user']], function() {
    Route::post('/update', [UserController::class, 'update']);
    Route::post('/list', [UserController::class, 'list']);
    Route::post('/delete', [UserController::class, 'delete']);
    Route::post('/logout', [AuthenticationController::class, 'userLogout'
]);

    // Post Route
    Route::group(['prefix' => 'post'], function() {
        Route::post('/create', [PostController::class, 'create']);
        Route::post('/read', [PostController::class, 'read']);
        Route::post('/update', [PostController::class, 'update']);
        Route::post('/list', [PostController::class, 'list']);
        Route::post('/delete', [PostController::class, 'delete']);
    });
});