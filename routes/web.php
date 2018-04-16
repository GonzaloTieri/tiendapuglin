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
Route::get('export', 'ExportCustomersController@index')->middleware('cors');
Route::post('main', 'ExportCustomersController@main')->middleware('cors');
Route::post('pushContacts', 'ExportCustomersController@pushContacts');