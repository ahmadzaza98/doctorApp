<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::group(['middleware' => ['api', 'changeLanguage']], function() {

    Route::group(['prefix' => 'admin'],function (){
    Route::POST('login' , [AuthController::class , 'login'])->name('login');
    Route::POST('logout' , [AuthController::class , 'logout'])->middleware('assign.guard:admin-api');
    Route::GET('index' , [AdminController::class , 'index']);
    Route::GET('posts' , [PostController::class , 'index']);
    Route::GET('Compost/{id}' , [PostController::class , 'showComments']);
    Route::POST('save' , [PostController::class, 'store'])->middleware('assign.guard:admin-api');

});


});
