
<?php

use Illuminate\Http\Request;

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/tests', function () {
        return response()->json([
            'Creator' => [
                'Programmer' => 'Peter'
            ]
        ]);
    });

    Route::resource('Approver', 'ApproverController');
    Route::resource('ChangeRestDay', 'ChangeRestDayController');
    Route::resource('ChangeShift', 'ChangeShiftController');
    Route::resource('Dtr', 'DtrController');
    Route::resource('EmployeeApprover', 'EmployeeApproverController');
    Route::resource('Employee', 'EmployeeController');
    Route::resource('Group', 'GroupController');
    Route::resource('LeaveBalance', 'LeaveBalanceController');
    Route::resource('Leave', 'LeaveController');
    Route::resource('LeaveType', 'LeaveTypeController');
    Route::resource('ManualAttendance', 'ManualAttendanceController');
    Route::resource('MissingTimeLog', 'MissingTimeLogController');
    Route::resource('OfficialBusiness', 'OfficialBusinessController');
    Route::resource('OverTime', 'OverTimeController');
    Route::resource('PayPeriod', 'PayPeriodController');
    Route::resource('Branch', 'BranchController');
    Route::resource('Calendar', 'CalendarEventController');
    Route::resource('Rate', 'RateController');
    Route::resource('Position', 'EmployeePositionController');
    Route::resource('Department', 'DepartmentController');
    Route::resource('RALog', 'RALogController');
    Route::resource('User', 'UserController');


    Route::post('user/updateRoles', 'UserController@updateRoles');
     Route::get('user/getRole/{id}', 'UserController@getRole');
    Route::post('user/ResetPassword', 'UserController@ResetPassword');

    Route::get('user/getUser/{email}', 'UserController@getUser');
    Route::get('getApplication/{type}/{approve_id}', 'UserController@getApplication');


    Route::post('Dtr/storeMultiple', 'DtrController@storeMultiple');
    Route::get('getDTR/{period_id}/{emp_id}', 'DtrController@getDTR');
    Route::get('getDTRinWorkDate/{work_date}/{emp_id}', 'DtrController@getDTRinWorkDate');
    Route::get('getDTRinRange/{from}/{to}/{emp_id}', 'DtrController@getDTRinRange');

    Route::put('getDTR_add/{emp_id}', 'DtrController@getDTR_add');


    Route::get('getBalance/{emp_id}/{leave_type_id}', 'LeaveBalanceController@getBalance');

    Route::get('getApprover/{emp_id}/{type}/{type_id}', 'ApproverController@getApprover');
    Route::get('getToApprove/{emp_id}', 'ApproverController@getToApprove');
    Route::get('getMyApp/{id}', 'ApproverController@getMyApp');
    Route::put('approveRequest', 'ApproverController@approveRequest');
    Route::put('disapproveRequest', 'ApproverController@disapproveRequest');

    Route::post('generatePayPeriod', 'PayPeriodController@generatePayPeriod');

    Route::post('Leave/cancelApp', 'LeaveController@cancelApp');
    Route::post('OverTime/cancelApp', 'OverTimeController@cancelApp');
    Route::post('ChangeShift/cancelApp', 'ChangeShiftController@cancelApp');
    Route::post('ChangeRestDay/cancelApp', 'ChangeRestDayController@cancelApp');
    Route::post('MissingTimeLog/cancelApp', 'MissingTimeLogController@cancelApp');
    Route::post('OfficialBusiness/cancelApp', 'OfficialBusinessController@cancelApp');
    Route::post('ManualAttendance/cancelApp', 'ManualAttendanceController@cancelApp');

    Route::post('updateRALog', 'RALogController@updateLog');
});
