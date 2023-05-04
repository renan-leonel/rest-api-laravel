<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/status', 'App\Http\Controllers\Api\ClientController@status');

Route::post('/clients/insert', 'App\Http\Controllers\Api\ClientController@insert');
Route::get('/clients/{id}', 'App\Http\Controllers\Api\ClientController@read');
Route::put('/clients/{id}', 'App\Http\Controllers\Api\ClientController@update');
Route::delete('/clients/{id}', 'App\Http\Controllers\Api\ClientController@delete');