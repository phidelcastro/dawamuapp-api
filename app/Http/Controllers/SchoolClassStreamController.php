<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SchoolClassStream;

class SchoolClassStreamController extends Controller
{
    //
    public function createStream(Request $request)
    {
        $request->validate([
            'school_class_id' => 'required|exists:school_classes,id',
            'stream_name' => 'required|string',
            'stream_code' => 'nullable|string',
            'stream_description' => 'nullable|string'
        ]);

        $stream = SchoolClassStream::create($request->all());
        return response()->json([
            'message' => 'Stream created successfully',
            'stream' => $stream
        ], 201);
    }
}
