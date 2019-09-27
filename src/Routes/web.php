<?php

use Illuminate\Support\Facades\Route;

// Internal UI
Route::middleware('web', 'auth:web')->namespace('Hanoivip\Proceed\Controllers')->group(function () {
    Route::get('/proc', 'ProceedController@home')->name('proceed');
    Route::post('/proc/exchange', 'ProceedController@exchange')->name('proceed.exchange');
});

// Public UI
Route::namespace('Hanoivip\Proceed\Controllers')->group(function () {
    Route::get('/proc/{code}', 'ProceedController@click');
    Route::post('/proc/click', 'ProceedController@doClick')->name('proceed.click');
});