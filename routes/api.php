<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/status', 'App\Http\Controllers\Api\ClientController@status');
Route::post('/clients/insert', 'App\Http\Controllers\Api\ClientController@insert');