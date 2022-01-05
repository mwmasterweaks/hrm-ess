<?php

namespace App\Http\Controllers;

use App\biometric_attendance;
use App\dtr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BiometricAttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $work_date = substr($request->punch_time, 0, 10);
        $type = $request->type == 'Time In' ? 'time_in' : 'time_out';
        $dtr = dtr::where('employee_id', $request->employee_id)
            ->where('work_date', $work_date);
        $dtr_count = (clone $dtr)->count();
        $dtr_row = (clone $dtr)->first();
        // return $dtr;
        if ($dtr_count > 0) {
            if ($dtr_row[$type] == NULL) {
                $bio = biometric_attendance::create($request->all());
                (clone $dtr)->update([$type => $request->punch_time]);

                if (true) {
                    $message = "
                    <html>
                    <head>
                    </head>
                    <body>
                    " . $request->msg . "
                    </body>
                    <style>
                    .my-td {
                    padding: 5px;
                    }
                    .my-table,
                    .my-td {
                    border: 1px solid slategrey;
                    }
                    .my-table {
                    border-collapse: collapse;
                    width: 100%;
                    }
                    </style>
                    </html>";
                    $message = str_replace("REFFFFFFFFF", "BA-" . $bio->id, $message);
                    $message = str_replace("biotype", $request->type, $message);
                    $message = str_replace("getbioid", $bio->id, $message);
                }

                \Logger::instance()->mailerZimbra(
                    "Biometric Approval",
                    $message,
                    $request->user_email,
                    $request->user_name,
                    $request->sendTo,
                    $request->CCto
                );

                return 1;
            } else return 2;
        } else return 0;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\biometric_attendance  $biometric_attendance
     * @return \Illuminate\Http\Response
     */
    public function show(biometric_attendance $biometric_attendance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\biometric_attendance  $biometric_attendance
     * @return \Illuminate\Http\Response
     */
    public function edit(biometric_attendance $biometric_attendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\biometric_attendance  $biometric_attendance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, biometric_attendance $biometric_attendance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\biometric_attendance  $biometric_attendance
     * @return \Illuminate\Http\Response
     */
    public function destroy(biometric_attendance $biometric_attendance)
    {
        //
    }
}
