<?php

namespace App\Http\Controllers;

use App\Http\Services\ParentMessageService;
use Illuminate\Http\Request;


class SchoolParentMessageController extends Controller
{
    protected $service;

    public function __construct(ParentMessageService $service)
    {
        $this->service = $service;
    }

    public function SendParentMessages(Request $request)
    {
        $validated = $request->validate([
            'types' => 'required|array|min:1',
            'types.*' => 'in:email,phone,push',
            'content' => 'required|string',
            'subject' => 'nullable|string',
            'parents' => 'required|array|min:1',
            'parents.*' => 'exists:guardians,id',
        ]);

        $message = $this->service->createAndDispatch($validated, auth()->id());

        return response()->json([
            'message' => 'Message saved and dispatch queued.',
            'data' => $message,
        ]);
    }
}
