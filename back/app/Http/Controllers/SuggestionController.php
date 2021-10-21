<?php

namespace App\Http\Controllers;

use App\Suggestion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuggestionController extends Controller
{
    private $cname = "SuggestionController";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      // $tbl =  Suggestion::all();
        $tbl = Suggestion::with(['user.employee'])->get();

        return response()->json($tbl);
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
        try {
            $suggestion = Suggestion::create($request->all());

            \Logger::instance()->log(
                Carbon::now(),
                $request->userid,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new suggestion with ID: " . $suggestion->id . "\nDetails: " .  $suggestion
            );

            return $this->index();
        } catch (\Exception $ex) {
            \Logger::instance()->logError(
                Carbon::now(),
                $request->userid,
                $request->user_name,
                $this->cname,
                "store",
                "Error",
                $ex->getMessage()
            );
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Suggestion  $suggestion
     * @return \Illuminate\Http\Response
     */
    public function show(Suggestion $suggestion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Suggestion  $suggestion
     * @return \Illuminate\Http\Response
     */
    public function edit(Suggestion $suggestion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Suggestion  $suggestion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {

            $cmd  = suggestion::findOrFail($id);
            $logFrom = (clone $cmd);
            $input = $request->all();
            $cmd->fill($input)->save();
            $logTo = $cmd;

            \Logger::instance()->log(
                Carbon::now(),
                $request->userid,
                $request->user_name,
                $this->cname,
                "update",
                "message",
                "Update suggestion with ID: " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
            );

            return $this->index();
        } catch (\Exception $ex) {
            \Logger::instance()->logError(
                Carbon::now(),
                $request->userid,
                $request->user_name,
                $this->cname,
                "update",
                "Error",
                $ex->getMessage()
            );
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }


    public function destroy($id)
    {
        try {

            suggestion::destroy($id);

            DB::table('suggestion_comments')->where('suggestion_id', $id)->delete();
            return $this->index();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->message], 500);
        }
    }
}
