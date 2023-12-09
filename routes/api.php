<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace' => 'API', 'prefix' => 'v1'], function(){
    Route::post('/register', 'ApiController@register');
    Route::post('/login', 'ApiController@login');
    Route::group(['middleware' => 'jwt.auth'], function (){
        Route::post('/candidates/search', 'ApiController@searchCandidate');
        Route::post('/logout', 'ApiController@logout');
        Route::post('/token/refresh', 'ApiController@refreshToken');
        Route::post('/candidates', 'ApiController@saveCandidates');
        Route::get('/candidates/{id}', 'ApiController@candidateData');
        Route::get('/candidates', 'ApiController@candidateList');
        Route::post('/candidates/{id}', 'ApiController@updateCandidate');
        Route::delete('/candidates/{id}', 'ApiController@deleteCandidate');
        
    });
});
