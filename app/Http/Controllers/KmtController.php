<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KmtController extends Controller
{
    public function callback(Request $request)
    {
        return response()->json(["status" => 200, "data" => [
            "user" => $request->all()
        ]]);
    }
}
