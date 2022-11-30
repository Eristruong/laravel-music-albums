<?php

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

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::post('find',[App\Http\Controllers\HomeController::class, 'find'])->name('find');  
Route::get('/musics', [App\Http\Controllers\HomeController::class, 'music'])->name('songs');
Route::get('music/{slug}', [App\Http\Controllers\HomeController::class, 'showmusic']);


Route::get('/admin', [App\Http\Controllers\AdminController::class, 'index'])->middleware('auth')->name('admin');

Route::group(['prefix'=>'admin', 'middleware'=>'AdminRole'], function(){
    Route::get('musics', [App\Http\Controllers\AdminController::class, 'music'])->name('music');
    });