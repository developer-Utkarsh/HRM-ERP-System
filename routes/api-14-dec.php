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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/users/check/mobile', 'UsersController@checkMobile');
Route::post('/users/faculty/schedule', 'UsersController@getFacultySchedule');

Route::get('/users/allnotifications', 'UsersController@getAllNotification');

Route::post('/users/profile/update', 'UsersController@ProfileUpdate');
Route::post('/users/faculty/dashboard', 'UsersController@Dashboard');
Route::post('/users/studio-assistant/dashboard', 'UsersController@Dashboard');//dk
Route::post('/users/studio-manager/dashboard', 'UsersController@Dashboard'); //dk

Route::post('/users/faculty/reschedule', 'ClassRequestController@facultyreschedule');
Route::post('/users/faculty/getrescheduleclass', 'ClassRequestController@getFacultyRescheduleClass');

Route::get('/users/getfaculty', 'ClassRequestController@getFaculty');
Route::post('/users/getsinglefacultyschedule', 'ClassRequestController@getSingleFacultySchedule');

Route::post('/users/faculty/swap', 'ClassRequestController@swap');
Route::post('/users/faculty/scheduleswap', 'ClassRequestController@scheduleswap');

Route::post('/users/faculty/cancelclass', 'ClassRequestController@cancelclass');
Route::post('/users/faculty/getcancelclass', 'ClassRequestController@GetCancelClass');

Route::post('/users/facluty/class/report', 'ReportController@getFacultyClassReport');

Route::post('/users/get/profile', 'UsersController@getProfile'); //dk

Route::post('/users/get_task', 'TaskController@get_task'); //dk
Route::post('/users/update_task', 'TaskController@update_task'); //dk
Route::post('/users/add_task', 'TaskController@add_task'); //dk
Route::post('/users/delete_task', 'TaskController@delete_task'); //dk
Route::post('/users/getemployeelist', 'UsersController@getemployeelist'); //dk
Route::post('/users/add_leave', 'LeaveController@add_leave'); //dk


