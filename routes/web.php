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

Route::get('/', function () {
    return view('welcome');
});

//登录注册
Route::post('/login', 'LoginController@userLogin');
Route::post('/logout', 'LoginController@deleteUserSession');

//注册路由
Route::post('/register', 'RegisterController@userRegister');
