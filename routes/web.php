<?php

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

Route::get('/{tag?}/{sort?}','InstagramController@index');

Route::get('/instagram/fetch/{page?}/{tag?}/{sort?}','InstagramController@fetch');

$router->group(['prefix'=>'api/v1'], function() use($router){
    Route::get('/instagram/feed','InstagramController@feed');
});
