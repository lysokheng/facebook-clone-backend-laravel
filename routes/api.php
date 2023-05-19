<?php

use App\Http\Controllers\UserAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


/// register user 
Route::post('register', [UserAuthController::class, 'register']);
/// login user
Route::post('login', [UserAuthController::class, 'login']);
/// logout user
Route::post('logout', [UserAuthController::class, 'logout'])->middleware('auth:api');
/// update user 
Route::post('user/update/{id}', [UserAuthController::class, 'update'])->middleware('auth:api');
/// delete user
Route::delete('user', [UserAuthController::class, 'delete'])->middleware('auth:api');

/// get current user logged 
Route::get('user', [UserAuthController::class, 'user'])->middleware('auth:api');
Route::get('users', [UserAuthController::class, 'getAllUsers'])->middleware('auth:api');

/// posts routes 

Route::get('posts', [PostController::class, 'index'])->middleware('auth:api');
Route::post('posts', [PostController::class, 'store'])->middleware('auth:api');
Route::post('posts/{id}', [PostController::class, 'update'])->middleware('auth:api');
Route::delete('posts/{id}', [PostController::class, 'destroy'])->middleware('auth:api');



/// like post 
Route::post('like', [LikeController::class, 'like'])->middleware('auth:api');
/// unlike post
Route::post('unlike', [LikeController::class, 'unlike'])->middleware('auth:api');

/// get post likes
Route::get('post/likes/{id}', [LikeController::class, 'getPostLikes'])->middleware('auth:api');

/// comment route 
Route::post('comment', [CommentController::class, 'store'])->middleware('auth:api');
Route::post('comment/{id}', [CommentController::class, 'update'])->middleware('auth:api');
Route::delete('comment/{id}', [CommentController::class, 'destroy'])->middleware('auth:api');
///show all comment by post 
Route::get('comments/{id}', [CommentController::class, 'showComments'])->middleware('auth:api');

