<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\registercontroller;
use App\Http\Controllers\authController;
use App\Http\Controllers\categoriescontroller;
use App\Http\Controllers\productcontroller;
use App\Http\Controllers\googlecontroller;



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
ROUTE::post('/login',[authController::class,'login']);
ROUTE::post('/register',[registercontroller::class,'register']);
ROUTE::get('/oauth/register',[googlecontroller::class,'redirect']);
ROUTE::get('/oauth/google/callback',[googlecontroller::class,'callback']);


ROUTE::middleware(['user'])->group(function(){

    ROUTE::get('/products',[productcontroller::class,'read']);
    ROUTE::post('/products',[productcontroller::class,'create']);
    ROUTE::put('/products/{id}',[productcontroller::class,'update']);
    ROUTE::delete('/products/{id}',[productcontroller::class,'delete']);

});

ROUTE::middleware(['adminmiddleware'])->group(function(){

    ROUTE::post('/categories',[categoriescontroller::class,'create']);
    ROUTE::get('/categories',[categoriescontroller::class,'read']);
    ROUTE::put('/categories/{id}',[categoriescontroller::class,'update']);
    ROUTE::delete('/categories/{id}',[categoriescontroller::class,'delete']);

});



