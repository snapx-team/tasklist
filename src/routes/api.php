<?php

Route::group(['namespace' => 'Xguard\Tasklist\Http\Controllers',], function () {
    Route::group(['prefix' => 'api/tasklist', 'as' => 'tasklist'], function () {

        Route::get('/get-task-list/{jobSiteShiftId}', 'TaskController@getEmployeeDailyTasks');
        Route::post('/create-completed-task', 'TaskController@createCompletedTask');

        Route::get('/get-all-active-contracts', 'ErpController@getAllActiveContracts');
        Route::get('/get-some-active-contracts/{searchTerm}', 'ErpController@getSomeActiveContracts');

        //TODO: To remove
        Route::get('/test', 'TaskController@getTest');

    });
});
