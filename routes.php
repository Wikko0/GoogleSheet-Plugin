<?php

// Client View Groups
Route::group(['middleware' => ['web'], 'namespace' => '\Wikko\Googlesheet\Controllers'], function () {
    Route::get('plugins/wikko/googlesheet', 'DashboardController@index');
    Route::get('googlesheet', 'GoogleController@index');
    Route::get('googlesheet/create', 'GoogleController@create');
    Route::get('googlesheet/listing', 'GoogleController@listing');
    Route::post('googlesheet/store', 'GoogleController@store');
    Route::post('googlesheet/delete', 'GoogleController@delete');
    Route::get('googlesheet/delete/confirm', 'GoogleController@deleteConfirm');
    Route::get('googlesheet/{id}/edit', 'GoogleController@edit');
    Route::post('googlesheet/{id}/update', 'GoogleController@update');
    Route::get('googlesheet/synchronize', 'GoogleController@synchronize');
    Route::get('googlesheet/install', 'GoogleController@install');
});
