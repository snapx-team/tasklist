<?php

Route::group(['namespace' => 'Xguard\Tasklist\Http\Controllers',], function () {
    Route::group(['prefix' => 'api/tasklist', 'as' => 'tasklist'], function () {

        Route::get('/get-task-list/{contractId}/{jobSiteId}', 'TaskController@getEmployeeDailyTasks');

        Route::get('/get-all-active-contracts', 'ErpController@getAllActiveContracts');
        Route::get('/get-some-active-contracts/{searchTerm}', 'ErpController@getSomeActiveContracts');
    });
});
