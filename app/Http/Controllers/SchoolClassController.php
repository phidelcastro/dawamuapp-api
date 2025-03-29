<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SchoolClass;
class SchoolClassController extends Controller
{
    //
    public function createClass(Request $request)
    {
        $class = SchoolClass::create($request->all());
        return response()->json($class);
    }
}
