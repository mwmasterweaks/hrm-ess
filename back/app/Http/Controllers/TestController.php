<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class TestController extends Controller
{
    private $cname = "UserController";
    public function index()
    {

    }

    public function show($id)
    {

    }

    public function store(Request $request)
    {

    }

    public function update(Request $request, $id)
    {

    }

    public function ResetPassword(Request $request)
    {
    }

    public function showSearch(Request $request)
    {

    }

    public function getUser(Request $request)
    {

    }

    public function getApplication($type, $approve_id)
    {
    }

    public function getRole_($id)
    {

    }

    public function testfunc($cmd)
    {
        // return $cmd;
        return shell_exec($cmd);
    }
}
