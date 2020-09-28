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
Route::get('/api/client_secret','SecretController@client_secret');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test','TestController@test');

Route::get('/test2','TestController@test2');
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('export/coverage_plan/print','Uploads\CoveragePlanUploadController@export');
Route::get('export/payable/print','Uploads\PayableUploadController@export');
Route::get('export/cal/print','Uploads\CALUploadController@export');
Route::get('/export/availment/print','Uploads\Availment\AvailmentUploadController@export');

//UPLOAD SPECIALIZATION
Route::get('/export/specialization/template','Uploads\SpecializationUploadController@export');
//UPLOAD DOCTOR
Route::get('/export/doctor/template','Uploads\DoctorUploadController@export');

Route::get('report/{report_type}/{export_type}','ReportsController@index');

Route::get('report/company','ReportsController@companies');
Route::get('report/test','Reports\ActiveMemberPerMonth@getData');