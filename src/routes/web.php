<?php

Route::group(['prefix' => 'tasklist', 'as' => 'tasklist'], function () {
    Route::group(['namespace' => 'Xguard\Tasklist\Http\Controllers',], function () {

        Route::group(['middleware' => ['web']], function () {

            // Setting sessions variables and checking if user is still logged in
            Route::get('/set-sessions', 'AppController@setTasklistSessionVariables');

            Route::group(['middleware' => ['tasklist_role_check']], function () {

                // We'll let vue router handle 404 (it will redirect to dashboard)
                Route::fallback('AppController@getIndex');

                // All view routes are handled by vue router
                Route::get('/', 'AppController@getIndex');

                // Employee App Data
                Route::get('/get-role-and-employee-id', 'AppController@getRoleAndEmployeeId');
                Route::get('/get-admin-page-data', 'AppController@getAdminPageData');
                Route::get('/get-employee-profile', 'AppController@getEmployeeProfile');
                Route::get('/get-footer-info', 'AppController@getFooterInfo');

                // Employees
                Route::post('/create-employees', 'EmployeeController@createEmployees');
                Route::post('/delete-employee/{id}', 'EmployeeController@deleteEmployee');
                Route::get('/get-employees', 'EmployeeController@getEmployees');

                // Tasks
                Route::post('/create-task', 'TaskController@createTask');
                Route::post('/delete-task/{id}', 'TaskController@deleteTask');
                Route::post('/edit-task/{id}', 'TaskController@editTask');
                Route::get('/get-global-contract-tasks/{id}', 'TaskController@getGlobalContractTasks');
                Route::get('/get-job-site-tasks/{id}', 'TaskController@getJobSiteTasks');

                //ERP Data
                Route::get('/get-all-users', 'ErpController@getAllUsers');
                Route::get('/get-some-users/{searchTerm}', 'ErpController@getSomeUsers');
                Route::get('/get-all-active-contracts', 'ErpController@getAllActiveContracts');
                Route::get('/get-some-active-contracts/{searchTerm}', 'ErpController@getSomeActiveContracts');
            });
        });
    });
});
