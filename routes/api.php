<?php

use App\Http\Controllers\AllController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::prefix('/recriuter')->group(function(){
Route::post('/register',[AllController::class,'register']);
Route::post('/login',[AllController::class,'login']);
Route::post('/logout',[AllController::class,'logout'])->middleware('auth:sanctum');
Route::get('/show/{id}',[AllController::class, 'show']);
Route::get('/allViewData',[AllController::class, 'allViewData']);
Route::put('update/{id}', [AllController::class, 'updateData'])->middleware('auth:sanctum');
Route::post('/create_job',[AllController::class,'create_job']);


});