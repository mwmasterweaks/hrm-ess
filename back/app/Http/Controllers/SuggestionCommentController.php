<?php

namespace App\Http\Controllers;

use App\Suggestion_comment;
use Illuminate\Http\Request;

class SuggestionCommentController extends Controller
{

    public function index()
    {
        $tbl = Suggestion_comment::all();

        return response()->json($tbl);
    }


    public function create()
    {

    }

    public function store(Request $request)
    {
        try {
            $data = Suggestion_comment::create($request->all());

            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new Suggestion_Comment: " . $data
            );

            return $this->getComments($request->suggestion_id);
        } catch (\Exception $ex) {
            \Logger::instance()->logError(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "Error",
                $ex->getMessage()
            );
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }


    public function show(Suggestion_comment $suggestion_comment)
    {

    }


    public function edit(Suggestion_comment $suggestion_comment)
    {
        //
    }

        public function update(Request $request, $id)
    {
        try {

            $cmd  = Suggestion_comment::findOrFail($id);
            $logFrom = $cmd->replicate();
            $input = $request->all();

            $cmd->fill($input)->save();

            $logTo = $cmd;

            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "update",
                "message",
                "update Suggestion_Comment id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
            );

            return response()->json($this->index());
        } catch (\Exception $ex) {
            \Logger::instance()->logError(
                Carbon::now(),
                $request->user_id,
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
            $tbl1 = Suggestion_comment::findOrFail($id);
            Suggestion_comment::destroy($id);

            \Logger::instance()->log(
                Carbon::now(),
                "",
                "",
                $this->cname,
                "destroy",
                "message",
                "delete Suggestion_Comment id " . $id .
                    "\nOld Suggestion_Comment: " . $tbl1
            );

            return response()->json($this->index());
        } catch (\Exception $e) {
            \Logger::instance()->logError(
                Carbon::now(),
                "",
                "",
                $this->cname,
                "destroy",
                "Error",
                $e->getMessage()
            );
            return response()->json(['error' => 'There was a problem deleting your comment.'], 500);
        }
    }


    public function getComments($s_id)
    {
        $tbl = Suggestion_comment::with(["user"])->where('suggestion_id', $s_id)->orderBy('updated_at', 'DESC')->get();

        return response()->json($tbl);
    }
}
