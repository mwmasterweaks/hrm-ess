<?php

namespace App\Http\Controllers;

use App\leave_update_history;
use Illuminate\Http\Request;

class LeaveUpdateHistoryController extends Controller
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
     * @param  \App\leave_update_history  $leave_update_history
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return leave_update_history::with(['user', 'employee'])
            ->where('employee_id', $id)
            ->get();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\leave_update_history  $leave_update_history
     * @return \Illuminate\Http\Response
     */
    public function edit(leave_update_history $leave_update_history)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\leave_update_history  $leave_update_history
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, leave_update_history $leave_update_history)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\leave_update_history  $leave_update_history
     * @return \Illuminate\Http\Response
     */
    public function destroy(leave_update_history $leave_update_history)
    {
        //
    }
}
