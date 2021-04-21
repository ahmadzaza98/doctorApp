<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DrugController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use App\Http\Controllers\VerificationController;
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


    Route::GET('email/verify/{id}/{hash}', [VerificationController::class ,'verify'])->name('verification.verify'); // Make sure to keep this as your route name
    Route::GET('email/resend', [VerificationController::class,'resend'])->name('verification.resend');

    Route::group(['prefix' => 'user'],function (){
        Route::POST('register' , [AuthController::class , 'UserRegister']);
    });

    Route::POST('data' ,[PostController::class, 'addData']);
    Route::group(['prefix' => 'admin'],function (){
    Route::POST('login' , [AuthController::class , 'login'])->name('login');
    Route::POST('register' , [AuthController::class , 'register']);
    Route::POST('logout' , [AuthController::class , 'logout'])->middleware('assign.guard:admin-api');
    Route::GET('index' , [AdminController::class , 'index']);
    Route::GET('posts' , [PostController::class , 'index']);
    Route::GET('Compost/{id}' , [PostController::class , 'showComments']);
    Route::POST('save' , [PostController::class, 'store'])->middleware('assign.guard:admin-api');
    Route::post('drugs/{patient_id}',[]);
    Route::get('permanent-drugs/{patient_id}' ,[DrugController::class , 'getPermanent'])->middleware('assign.guard:admin-api');

});


});
