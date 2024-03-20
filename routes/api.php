<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Jobs\StreamRealTimeData;
/*
|--------------------------------------------------------------------------
| BREAD API DESIGN: BROWSE READ EDIT ADD DELETE 
|--------------------------------------------------------------------------
*/




Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Post List methods
Route::get('/posts','App\Http\Controllers\PostController@getAllPost');
Route::get('/posts/{postid}','App\Http\Controllers\PostController@getPostById');

// Post Update method
Route::get('/posts/{postid}/add-retweet','App\Http\Controllers\PostController@newRetweet');
Route::get('/posts/{postid}/add-like','App\Http\Controllers\PostController@newLike');
Route::post('/posts/new','App\Http\Controllers\PostController@addNewPost');
Route::post('/posts/{postid}/add-hastags','App\Http\Controllers\PostController@addNewHashtag');

// Post Realtime streaming: update to displatch the process to an async Job
// "App\Http\Controllers\SseDataPusher@getDataStream",
 // return response()->json(['message' => 'Streaming real-time data updates has been initiated.']);
Route::get('/stream/post', function (){
    StreamRealTimeData::dispatch();
});