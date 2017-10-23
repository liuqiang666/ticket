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
Route::post('/register', 'RegisterController@userRegister');

//余票查询
Route::post('/ticket', 'ScheduleController@getSeatCount');

//车站名联想
Route::post('/station/association', 'ScheduleController@getAssociateStationName');
//时刻表
Route::post('/schedule', 'ScheduleController@getSchedule');

//车票
Route::post('/ticket/generate', 'TicketController@generateTicket');
Route::post('/ticket/refund', 'TicketController@refundTicket');

//订单
Route::post('/order/generate', 'OrderController@generateOrder');
Route::post('/order', 'OrderController@getOrderInfo');
Route::post('/order/status', 'OrderController@changeOrderStatus');

//乘客
Route::post('/passenger/add', 'PassengerController@addPassenger');
Route::post('/passenger/update', 'PassengerController@changePassengerInfo');
Route::post('/passenger', 'PassengerController@getPassengerOfUser');
Route::post('/passenger/delete', 'PassengerController@deletePassenger');
