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

Route::get('/upload/csv', "App\Http\Controllers\UtilMethod@parseCsvtoJSON");
Route::get('/wordcloud',"App\Http\Controllers\UtilMethod@generateWordCloud");

// =========================================================================
Route::get('/test', "App\Http\Controllers\SseDataPusher@test");
Route::get('/test/other', "App\Http\Controllers\UtilMethod@noMatter");