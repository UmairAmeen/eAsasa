<?php

Route::group(['middleware' => ['web','auth'], 'prefix' => 'hr', 'namespace' => 'Modules\HumanResource\Http\Controllers'], function()
{

    Route::get('/check_in/{id}', 'AttendanceController@check_in');
    Route::get('/check_out/{id}', 'AttendanceController@check_out');
    Route::get('/attendances_datatable', 'AttendanceController@datatable');
    Route::get('/attendances', 'AttendanceController@index');
    Route::get('/attendance', 'AttendanceController@attendance');
    Route::get('/attendance/add', 'AttendanceController@create');
    Route::post('/all_attendance', 'AttendanceController@all_attendance');
    Route::post('/single_attendance', 'AttendanceController@single_attendance');
    Route::post('/add_attendance', 'AttendanceController@store');
    Route::post('/add_holiday','HumanResourceController@addholiday');
    Route::get('/pholidays','HumanResourceController@publicholiday');
    Route::get('/attendance/edit/{id}', 'AttendanceController@edit');
    Route::post('/update_attendance', 'AttendanceController@update');
    Route::delete('delete_attendance/{id}','AttendanceController@delete');
    Route::delete('delete_holiday/{id}','HumanResourceController@deleteholiday');

    Route::get('/leaves_datatable', 'LeaveController@datatable');
    Route::get('/leaves', 'LeaveController@index');
    Route::get('/leave', 'LeaveController@create');
    Route::get('/leave/mul', 'LeaveController@multileave');
    Route::post('/leave', 'LeaveController@store');
    Route::post('/multipleleave','LeaveController@storemultileave');
    Route::delete('delete_leave/{id}','LeaveController@destroy');

    Route::get('/bonus/{id}', 'HumanResourceController@view_bonus');
    Route::get('/bonuslist', 'HumanResourceController@view_bonus_list');

    Route::post('/update_bonus', 'HumanResourceController@update_bonus');
    Route::post('/add_bonus', 'HumanResourceController@add_bonus');
    Route::get('/download_employee_list', 'HumanResourceController@download_employee_list');
    Route::get('/download_attendance_list', 'HumanResourceController@download_attendance_list');
    Route::get('/download_attendance_summary', 'HumanResourceController@download_attendance_summary');
    
    Route::post('/employee_list', 'HumanResourceController@employee_list');
    Route::post('/attendance_list', 'HumanResourceController@attendance_list');
    Route::post('/attendance_summary', 'HumanResourceController@attendance_summary');
    Route::get('/salary_list', 'HumanResourceController@salary_list');
    Route::get('/salary', 'HumanResourceController@salary');
    Route::get('/salary/{id}','HumanResourceController@view_salary');
    Route::post('/salary/{id}/pay','HumanResourceController@release_pay');
    Route::delete('/salary/{id}/deleted','HumanResourceController@delete_released');
    Route::post('/salary/{id}/edit','HumanResourceController@edit_released');
    Route::get('/salary/{id}/edit','HumanResourceController@view_edit');
    Route::get('/salary/pay/all','HumanResourceController@allpaid');

    Route::get('/bonus_list', 'HumanResourceController@bonus_list');
    
    Route::get('/', 'HumanResourceController@index')->name('hr.index');
    Route::get('/datatable', 'HumanResourceController@datatable');
    Route::get('/create', 'HumanResourceController@create');
    Route::post('/store', 'HumanResourceController@store');
    Route::get('/show/{id}', 'HumanResourceController@show');
    Route::get('/edit/{id}', 'HumanResourceController@edit');
    Route::put('/{id}', 'HumanResourceController@update');
    Route::post('/update', 'HumanResourceController@update');
    Route::delete('del/{id}','HumanResourceController@destroy');
    Route::delete('delbonus/{id}','HumanResourceController@deletebonus');
    Route::get('downloadExcel', 'HumanResourceController@downloadExcel');
    Route::post('importExcel', 'HumanResourceController@importExcel');

});

Route::group(['prefix'=>'api',  'namespace' => 'Modules\HumanResource\Http\Controllers'],function(){
    Route::post('machine_attendance', 'AttendanceController@machine_attendance');
    Route::get('att', 'AttendanceController@attendanceapi');
});