<?php

namespace App\Http\Controllers;

use App\biometric_attendance;
use Illuminate\Http\Request;

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
        $bio = biometric_attendance::create($request->all());
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
        //return $request;
        return \Logger::instance()->mailerZimbra(
            "Biometric Approval",
            $message,
            $request->user_email,
            $request->user_name,
            $request->sendTo,
            $request->CCto
        );
        // walay logket
        return $bio;
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
