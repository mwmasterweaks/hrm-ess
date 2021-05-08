
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
    Route::resource('ShiftSchedule', 'ShiftScheduleController');
    Route::resource('Suggestion', 'SuggestionController');
    Route::resource('SuggestionComment', 'SuggestionCommentController');
    Route::resource('Deduction', 'DeductionController');
    Route::resource('DeductionType', 'DeductionTypeController');
    Route::resource('Earning', 'EarningController');
    Route::resource('EarningType', 'EarningTypeController');
    Route::resource('Payslip', 'PayslipController');




    Route::post('user/updateRoles', 'EmployeeController@updateRoles');
    Route::post('user/multipleFilter', 'EmployeeController@multipleFilter');
    Route::post('Employee/sendCredentials', 'EmployeeController@sendCredentials');

    Route::get('user/getRole/{id}', 'UserController@getRole');
    Route::post('user/ResetPassword', 'UserController@ResetPassword');

    Route::get('user/getUser/{email}', 'UserController@getUser');
    Route::get('getApplication/{type}/{approve_id}', 'UserController@getApplication');


    Route::post('Dtr/storeMultiple', 'DtrController@storeMultiple');
    Route::get('getDTR/{period_id}/{emp_id}/{user_id}/{user_name}', 'DtrController@getDTR');
    Route::get('getDTRinWorkDate/{work_date}/{emp_id}', 'DtrController@getDTRinWorkDate');
    Route::get('getDTRinRange/{from}/{to}/{emp_id}', 'DtrController@getDTRinRange');

    Route::put('getDTR_add/{emp_id}', 'DtrController@getDTR_add');
    Route::put('HRSummaryReport/{period_id}', 'DtrController@HRSummaryReport');


    Route::get('getBalance/{emp_id}/{leave_type_id}', 'LeaveBalanceController@getBalance');

    Route::get('getApprover/{emp_id}/{type}/{type_id}', 'ApproverController@getApprover');
    Route::get('getToApprove/{emp_id}', 'ApproverController@getToApprove');
    Route::get('getMyApp/{id}', 'ApproverController@getMyApp');
    Route::put('approveRequest', 'ApproverController@approveRequest');
    Route::put('disapproveRequest', 'ApproverController@disapproveRequest');
    Route::put('getComments/{s_id}', 'SuggestionCommentController@getComments');

    Route::post('generatePayPeriod', 'PayPeriodController@generatePayPeriod');
    Route::post('PayPeriod/getYear', 'PayPeriodController@getYear');
    Route::get('PayPeriod/getMonth/{year}', 'PayPeriodController@getMonth');
    Route::get('PayPeriod/getDay/{yearMonth}', 'PayPeriodController@getDay');

    Route::post('Leave/cancelApp', 'LeaveController@cancelApp');
    Route::post('OverTime/cancelApp', 'OverTimeController@cancelApp');
    Route::post('ChangeShift/cancelApp', 'ChangeShiftController@cancelApp');
    Route::post('ChangeRestDay/cancelApp', 'ChangeRestDayController@cancelApp');
    Route::post('MissingTimeLog/cancelApp', 'MissingTimeLogController@cancelApp');
    Route::post('OfficialBusiness/cancelApp', 'OfficialBusinessController@cancelApp');
    Route::post('ManualAttendance/cancelApp', 'ManualAttendanceController@cancelApp');

    Route::post('updateRALog', 'RALogController@updateLog');

    Route::post('Deduction/destroyItem', 'DeductionController@destroyItem');
    Route::post('Earning/destroyItem', 'EarningController@destroyItem');
    Route::post('Payslip/generatePayslip', 'PayslipController@generatePayslip');

    Route::post('EmployeeApprover/storeMultiple', 'EmployeeApproverController@storeMultiple');


});
