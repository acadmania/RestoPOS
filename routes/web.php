<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard',[App\Http\Controllers\HomeController::class, 'tenantList'] )->middleware(['auth']);
Route::get('/add-new-client',[App\Http\Controllers\HomeController::class, 'tenantAdd'] )->middleware(['auth']);
Route::post('/tenant-add',[App\Http\Controllers\HomeController::class, 'tenantStore'] )->middleware(['auth']);
Route::get('/delete/tenant/{id}',[App\Http\Controllers\HomeController::class, 'tenantDelete'] )->middleware(['auth']);
Route::get('/tenant/message/{id}',[App\Http\Controllers\HomeController::class, 'tenantMessageForm'] )->middleware(['auth']);
Route::post('/tenant/message',[App\Http\Controllers\HomeController::class, 'tenantMessageStore'] )->middleware(['auth']);

require __DIR__.'/auth.php';
