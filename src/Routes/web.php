<?php

use Illuminate\Support\Facades\Route;

// Internal UI
Route::middleware('web', 'auth:web')->namespace('Hanoivip\Proceed\Controllers')->group(function () {
    Route::get('/proc/home', 'ProceedController@home')->name('proceed');
    Route::post('/proc/exchange', 'ProceedController@exchange')->name('proceed.exchange');
    Route::get('/proc/history', 'ProceedController@history')->name('proceed.history');
});

// Public UI
Route::namespace('Hanoivip\Proceed\Controllers')->group(function () {
    Route::get('/proc/{code}', 'ProceedController@click');
    Route::get('/proc', 'ProceedController@click2');
    Route::post('/proc/click', 'ProceedController@doClick')->name('proceed.click');
});