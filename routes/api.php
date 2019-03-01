<?php

use Illuminate\Http\Request;

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

Route::get('archive', 'DecompileController@archive');
Route::get('browse', 'DecompileController@browse');
Route::post('decompile', 'DecompileController@handle');
Route::get('fetch', 'DecompileController@fetch');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
