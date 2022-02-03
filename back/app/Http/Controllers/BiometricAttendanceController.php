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
                date_default_timezone_set('Asia/Manila'); // CDT
                $punch_time = date('Y-m-d H:i:s');
                $bio = biometric_attendance::create($request->all());
                (clone $dtr)->update([$type => $punch_time]); // do not use $request->punch_time to avoid ui time recording

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
                                padding: 10px;
                                padding-left: 20px;
                                padding-right: 20px;
                            }
                            .my-table {
                                border-radius: 10px 10px 0 0;
                                border-bottom: 5px solid #547e6a;
                            }
                            .my-table,
                            .my-td {
                                background: #e7fff4;
                                font-family: 'Helvetica';
                            }
                            .head-bg {
                                color: #ffffff;
                                background: #098b4f;
                                border-radius: 10px 10px 0 0;
                                letter-spacing: 0.1em;
                                font-weight: bold;
                                padding: 20px;
                                padding-left: 40px;
                                padding-right: 40px;
                                text-align: center;
                            }
                            .name-bg {
                                color: #ffffff;
                                background: #3d3d3d;
                                /* font-weight: bold; */
                                text-align: left;
                            }
                            .tr-even {
                                background: #f1fff9;
                            }
                            .my-table {
                                border-collapse: collapse;
                            }
                        </style>
                    </html>";
                    $message = str_replace("REFNUM", "BA-" . $bio->id, $message);
                    $message = str_replace("BIOTYPE", $request->type, $message);
                    $message = str_replace("PUNCHTIME", $punch_time, $message);
                    // $message = str_replace("getbioid", $bio->id, $message);
                }

                \Logger::instance()->mailerZimbra(
                    "HRMESS - BIOMETRIC ATTENDANCE",
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
