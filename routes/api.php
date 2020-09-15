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


//Auth route
Route::post('register', 'AuthController@register');
Route::post('login', 'AuthController@login');
Route::group(['middleware' => 'jwt.auth'], function () {
    Route::get('logout', 'AuthController@logout');
    Route::get('profile', 'AuthController@user');
});

//User route
Route::group(['middleware'=>['jwt.auth'],'prefix'=>'user'], function () {
    Route::get('/','Api\UserController@index');
    Route::post('update/{id}','Api\UserController@update');
    Route::delete('delete/{id}','Api\UserController@destroy');
});


//Category route
Route::group(['middleware'=>['jwt.auth'],'prefix'=>'category'], function () {
    Route::get('/','Api\CategoryController@index');
    Route::get('/{id}/group','Api\CategoryController@show');
    Route::post('/add','Api\CategoryController@store');
    Route::post('update/{id}','Api\CategoryController@update');
    Route::delete('delete/{id}','Api\CategoryController@destroy');
});

//Group Category route
Route::group(['middleware'=>['jwt.auth'],'prefix'=>'group'], function () {
    Route::get('/', 'Api\GroupCategoryController@index');
    Route::get('/{id}/product', 'Api\GroupCategoryController@show');
    Route::post('/add', 'Api\GroupCategoryController@store');
    Route::delete('delete/{id}', 'Api\GroupCategoryController@destroy');
});

//Product route
Route::group(['middleware'=>['jwt.auth'],'prefix'=>'product'], function () {
    Route::get('/','Api\ProductController@index');
    Route::post('/add','Api\ProductController@store');
    Route::post('update/{id}','Api\ProductController@update');
    Route::delete('delete/{id}','Api\ProductController@destroy');
    Route::get('loadmore', 'Api\ProductController@loadData');
    Route::get('/{id}', 'Api\ProductController@show');
});

//handle order
Route::group(['middleware'=>['jwt.auth'],'prefix'=>'order'], function () {
    Route::get('/', 'Api\OrderController@index');
    Route::get('show/{id}', 'Api\OrderController@s  how');
    Route::get('detail/{id}', 'Api\OrderController@getOrderDetail');
    Route::post('create', 'Api\OrderController@createOrder');
    Route::post('update/{id}', 'Api\OrderController@update');
    Route::delete('delete/{id}', 'Api\OrderController@destroy');
});


