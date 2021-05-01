<?php

use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('upload-video');
});

Route::post('/upload','App\Http\Controllers\VideoController@uploadVideo')->name('upload-video');