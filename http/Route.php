<?php

Route::group(['middleware' => 'auth'], function () {

    Route::get('/', function () {
        return view('welcome');
    });
});
