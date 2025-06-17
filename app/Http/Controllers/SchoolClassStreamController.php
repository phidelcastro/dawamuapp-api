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
            '*.school_class_id' => 'required|exists:school_classes,id',
            '*.stream_name' => 'required|string',
            '*.stream_code' => 'nullable|string',
            '*.stream_description' => 'nullable|string',
        ]);
    
        $createdStreams = [];
    
        foreach ($request->all() as $streamData) {
            $createdStreams[] = \App\Models\SchoolClassStream::create($streamData);
        }
    
        return response()->json([
            'message' => 'Streams created successfully',
            'streams' => $createdStreams,
        ], 201);
    }
    
}
