<?php

    use Illuminate\Support\Facades\Route;
    
    // Internal UI
    Route::middleware('web', 'auth:web')->group(function () {
        Route::get('/proc', 'ProceedController@home');
        Route::post('/proc/exchange', 'ProceedController@exchange')->name('proceed.exchange');
    });

    // Public UI
    Route::group(function () {
        Route::get('/proc/{code}', 'ProceedController@click');
        Route::post('/proc/click', 'ProcessController@doClick')->name('proceed.click');
    });