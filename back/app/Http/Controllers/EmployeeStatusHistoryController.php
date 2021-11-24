<?php

namespace App\Http\Controllers;

use App\employee_status_history;
use Illuminate\Http\Request;

class EmployeeStatusHistoryController extends Controller
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\employee_status_history  $employee_status_history
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return employee_status_history::with(['employee'])
            ->where('employee_id', $id)
            ->get();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\employee_status_history  $employee_status_history
     * @return \Illuminate\Http\Response
     */
    public function edit(employee_status_history $employee_status_history)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\employee_status_history  $employee_status_history
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, employee_status_history $employee_status_history)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\employee_status_history  $employee_status_history
     * @return \Illuminate\Http\Response
     */
    public function destroy(employee_status_history $employee_status_history)
    {
        //
    }
}
