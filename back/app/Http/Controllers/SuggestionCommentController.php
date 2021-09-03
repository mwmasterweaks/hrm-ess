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
            Suggestion_comment::create($request->all());
            return $this->getComments($request->suggestion_id);
        } catch (\Exception $ex) {
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

            $input = $request->all();

            $cmd->fill($input)->save();

            return response()->json($this->index());
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    
    public function destroy($id)
    {
        try {
            Suggestion_comment::destroy($id);

            return response()->json($this->index());
        } catch (\Exception $e) {
            return response()->json(['error' => 'There was a problem deleting your comment.'], 500);
        }
    }


    public function getComments($s_id)
    {
        $tbl = Suggestion_comment::with(["user"])->where('suggestion_id', $s_id)->orderBy('updated_at', 'DESC')->get();

        return response()->json($tbl);
    }
}
